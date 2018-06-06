/** Klient POP3 s podporou TLS
* @file aux_functions.cpp
* @author Korček Juraj <xkorce01@stud.fit.vutbr.cz>
*/
#include "header.h"

// Test wheteher directory exists
int test_dir(string dir){
	struct stat info;
	if( stat( dir.c_str(), &info ) != 0 ){ //Fill stat struct
		return 1;
	} else if( info.st_mode & S_IFDIR ) {
		return 0;  
	} else return 2;

}

//Find string inside string on nth position
int find_position(string s, int nth_pos, string match, int len){
	int count = 0;
	string::size_type i;

	for (i = 0; i < s.size();i++) {
		if (!s.compare(i,len,match)) count++;
		if (count == nth_pos) break;		
	}
	return i;
}

int max_file_num(string out_dir){
    struct dirent *dir_struct;
    DIR *direct;
    int max = 0;
    string str;
    if( direct=opendir(out_dir.c_str()) ){
        while(dir_struct = readdir(direct)){
            if(reg_compare(dir_struct->d_name,"^[0-9]+$") == 0){
            	if ((stoi(dir_struct->d_name)) > max) {
            		max = stoi(dir_struct->d_name);
            	}
            }
        }
        closedir(direct);
   	}
   	return max;
}   	

void help_text(void){
	cout<<"Usage: popcl <server> [-p <port>] [-T|-S [-c <certfile>] [-C <certaddr>]] [-d] [-n] -a <auth_file> -o <out_dir> [-h] "<<endl;
	cout<<""<<endl;
	cout<<"  <server>             specifies IP address or hostname of POP3 server"<<endl;
	cout<<"  [-p <port>]          port to be used for connection, default 110 for unencrypted and STLS, 995 for pop3s"<<endl;
	cout<<"  [-T]                 turn on encrypted communication (pop3s)"<<endl;
	cout<<"  [-S] sends STLS      command to start encrypted connection"<<endl;
	cout<<"  [-c <certfile>]      specifies cert file which will be used to verify servers certificate, can be used only with -T or -S"<<endl;
	cout<<"  [-C <certaddr>]      specifies cert direcotry in which will be searched for cert to verify servers certificate, can be used only with -T or -S"<<endl;
	cout<<"  [-d]                 sends command for message deltetion"<<endl;
	cout<<"  [-n]                 downloads only new messsages"<<endl;
	cout<<"  -a <auth_file>       specifies file with username and password"<<endl;
	cout<<"  -o <out_dir>         specifies directory, where emails will be stored, it must exists"<<endl;
	cout<<"  -h                   help messsage"<<endl;
}

//Test whether regex is in string
int reg_compare (string str, string reg_str){
	int status_re;
    regex_t re;
 
    if (regcomp(&re, reg_str.c_str(), REG_EXTENDED|REG_NOSUB) != 0) return 1;
    status_re = regexec(&re, str.c_str(), (size_t) 0, NULL, 0);
    regfree(&re);

    if (status_re != 0) return 1;
    return 0;
}

//Deletes first line of string
void erase_first_line(string *msg){
	int pos = find_position(*msg, 1, "\n", 1);
	msg->erase(0,pos+1);
}

//Parse authentication file
int authparse(const char *file_name, string *user, string *pass){
	string auth[2];
	int i = 0;

	ifstream infile ( file_name );
	if (! infile.is_open()){
		cerr<<"ERROR: cannot open auth file, cannot login to server."<<endl;
		return 1;
	}
	while ((i<2) && (getline(infile , auth[i]))){
        i++;
    }
    infile.close();
    
    if (reg_compare(auth[0], "^username = .*$") == 1){ //Finding username
    	cerr<<"ERROR: wrong auth file format, cannot login to server."<<endl;
    	return 1;
    }
    if (reg_compare(auth[1], "^password = .*$") == 1){ //Finding password
    	cerr<<"ERROR: wrong auth file format, cannot login to server."<<endl;
    	return 1;
    }
    *user = auth[0].erase(0,11);
    *pass = auth[1].erase(0,11);
 	return 0;
}