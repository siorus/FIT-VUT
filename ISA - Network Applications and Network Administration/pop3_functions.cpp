/** Klient POP3 s podporou TLS
* @file pop3_functions.cpp
* @author Korček Juraj <xkorce01@stud.fit.vutbr.cz>
*/
#include "header.h"

//Sending username and password to authenticate user on server
int auth_pop_server(int *socket_fd, string auth_path, SSL* (&ssl)){
	string user, pass, msg;

	if ((authparse(auth_path.c_str(),&user,&pass)) != 0) return 1;
	if (send_recv_msg(socket_fd,&msg,ssl,("user "+user+"\r\n"),2) == 1) return 1;
	if (send_recv_msg(socket_fd,&msg,ssl,("pass "+pass+"\r\n"),2) == 1) return 1;
	if (reg_compare (msg, "^\\+OK .*$") == 1){
		cerr<<"ERROR: username/password not correct."<<endl;
		return 1;
	}
	return 0;
}

//Get number of messages on server
int list_msgs (int *socket_fd, long int *num_of_messages, SSL* (&ssl)){
	string msg;
	*num_of_messages = 0;

	if (send_recv_msg(socket_fd,&msg,ssl,"STAT\r\n",2) == 1) return 1;

	if (reg_compare(msg,"^\\+OK 0 messages:.*$") == 0){
		cout<<"No messages to download."<<endl;
	} else if (reg_compare(msg,"^\\+OK [0-9]* .*$") == 0) {
		int frst,scnd;
		frst = find_position(msg,1," ",1)+1;
		scnd = (find_position(msg,2," ",1))-frst;
		*num_of_messages = atol((msg.substr(frst, scnd)).c_str());
	}
	return 0;
}

//Get array of uidl of each email
int get_uid_list(int *socket_fd, int num_of_messages, string *uid_email_tab, SSL* (&ssl)){
	string msg,temp;
	int i = 0,start,end;
	if (send_recv_msg(socket_fd,&msg,ssl,("UIDL\r\n"),0) == 1)return 1;
	if (recv_in_loop(socket_fd, &msg, ssl) == 1) return 1;
	if (reg_compare (msg, "^\\+OK.*$") == 1){
		return 2; //UIDL is not supported
	}
	erase_first_line(&msg);
	istringstream iss(msg);
	while ((i < num_of_messages) && getline(iss,temp)){
		start = find_position(temp,1," ",1)+1;
		end = find_position(temp,1,"\r",1)-start;
		uid_email_tab[i]=temp.substr(start,end);
		i++;
	}
	return 0;
}

//Delete specific message on the server
int delete_message(int *socket_fd, int message_no, SSL* (&ssl)){
	string msg;
	if (send_recv_msg(socket_fd,&msg,ssl,"DELE "+(to_string(message_no))+"\r\n", 2) == 1) return 1;
}

//Fonction for comaparing local messages UIDL with remote on the server 
void mark_new_messages(string *uid_email_tab, string out_dir, int num_of_messages, long int *num_of_marked_messages){
	DIR *dir;
	struct dirent *ent;
	for (int i=0;i<num_of_messages;i++){
		if ((dir = opendir (out_dir.c_str())) != NULL) {
		  while ((ent = readdir (dir)) != NULL) {
		    if (uid_email_tab[i] == (ent->d_name)){
		    	uid_email_tab[i] = "OK"; //Mark already downloaded with OK flag, rmeove UIDL
		    	(*num_of_marked_messages)++;
		    }
		  }
		  closedir (dir);
		}
	}
}

//Get messages from the server
int download_messages (int *socket_fd, bool n, bool d, string out_dir, SSL* (&ssl)){
	long int num_of_messages, num_of_marked_messages = 0;
	int i=1,uidl_ret,max;
	string msg,line;
	ofstream outfile;
	if (list_msgs(socket_fd, &num_of_messages, ssl) == 1) return 1;

	string uid_email_tab[num_of_messages];
	uidl_ret = get_uid_list(socket_fd, num_of_messages, uid_email_tab, ssl);
	if ( uidl_ret == 1) return 1;
	if ((n==1) && (uidl_ret == 2)){
		cerr<<"ERROR: server does not support downloading new emails, run it without -n argument."<<endl;
		return 1;
	} else if ((n==1) && (uidl_ret != 2)) mark_new_messages(uid_email_tab,out_dir,num_of_messages, &num_of_marked_messages);

	for (i;i<=num_of_messages;i++){
		if (uidl_ret != 2){
			if ((n == 1) && (uid_email_tab[i-1] == "OK")) continue;
		}
		if (send_recv_msg(socket_fd,&msg,ssl,("RETR "+(to_string(i))+"\r\n"),0) == 1){
			return 1;
		}
		if (recv_in_loop(socket_fd, &msg, ssl) == 1) return 1;
		
		if (uidl_ret != 2){
			outfile.open(out_dir+"/"+uid_email_tab[i-1]); //new file name is from UIDL
		} else {
			max = (max_file_num(out_dir))+1;
			outfile.open(out_dir+"/"+to_string(max));
		}
		if (! outfile.is_open()){
			cerr<<"ERROR: writing to file failed."<<endl;
			return 1;
		}
		erase_first_line(&msg);
		msg.erase(find_position(msg,1,"\r\n.\r\n",5),5); //Erase ending CRLF.CRLF
		msg.erase(find_position(msg,1,"\r\n.\r\n",5),5);
		/*while (getline(msg,line)) {
			if (reg_compare(msg,"^\\.\\..*$")){

			}
		}*/
	    for(string::size_type j = 0; (j = msg.find("\r\n..", j)) != string::npos;)
	    {
	        msg.replace(j, 4, "\r\n.");
	        j = j + 3;
	    }

		outfile<<msg + "\r"<<endl;
		outfile.close();
		msg.clear();
	}
	if (d == 1)	{
		i = 1;
		for (i;i<=num_of_messages;i++) delete_message(socket_fd,i, ssl); //deletes message
	}

	if (n == 1){
		cout<<"Number of downloaded new messages: "+(to_string(num_of_messages-num_of_marked_messages))<<endl;
	} else cout<<"Number of downloaded messages: "+(to_string(num_of_messages))<<endl;
	return 0;
}
