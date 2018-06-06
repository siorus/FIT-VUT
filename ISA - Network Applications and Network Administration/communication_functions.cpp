/** Klient POP3 s podporou TLS
* @file communication_functions.cpp
* @author Korček Juraj <xkorce01@stud.fit.vutbr.cz>
*/
#include "header.h"

//Create socket and connect to client
int create_connect_socket (int *socket_fd, string ip_addr, int port, int tls_bool, int starttls_bool, SSL_CTX* (&ctx), SSL* (&ssl), string cert_file, int cert_file_bool, string cert_path, int cert_path_bool){
	string msg;
	struct addrinfo hints; //Structure for input of getaddrinfo
	struct addrinfo *result; //Structure for result of getaddrinfo
	sockaddr_in send_sock_addr;
	sockaddr_in6 send_sock_addr_6;



	//Init struct
	memset(&hints,0,sizeof(struct addrinfo));
	hints.ai_flags=AI_ADDRCONFIG;
	hints.ai_family = AF_UNSPEC;
	hints.ai_socktype = SOCK_STREAM;
	hints.ai_protocol=0;

	if (getaddrinfo(ip_addr.c_str(),(to_string(port)).c_str(),&hints, &result) != 0) { //Resolve IP/Hostname, fullfil protocols
		cerr<<"ERROR: problem resolving ip address."<<endl;
		return 1;
	}

	if (result->ai_family == AF_INET) {
		memset(&send_sock_addr, 0, sizeof(sockaddr_in));
		memcpy(&(send_sock_addr), result->ai_addr, result->ai_addrlen);
	}
	if (result->ai_family == AF_INET6) {
		memset(&send_sock_addr_6, 0, sizeof(sockaddr_in6));
  		memcpy(&(send_sock_addr_6), result->ai_addr, result->ai_addrlen);
	}
	if ((*socket_fd = socket(result->ai_family, SOCK_STREAM,0)) < 0) { //Creation of socket
		cerr<<"ERROR: creating socket."<<endl;
		return 1;
	}


	if (result->ai_family == AF_INET){
		if ((connect(*socket_fd,(sockaddr *)&send_sock_addr, sizeof(send_sock_addr))) < 0){ //Connection to server
			cerr<<"ERROR: cannot connect to server."<<endl;
			return 1;
		}
	} else {
		if ((connect(*socket_fd,(sockaddr *)&send_sock_addr_6, sizeof(send_sock_addr_6))) < 0){ //Connection to server
			cerr<<"ERROR: cannot connect to server."<<endl;
			return 1;
		}
	}
	freeaddrinfo(result);
	//TLS connect
	if (tls_bool == 1) {
		if (init_ctx(ctx) == 1) return 1;
		if (connect_ssl(ctx, ssl,socket_fd, cert_file, cert_file_bool, cert_path, cert_path_bool) == 1) return 1 ;
	}
	if (send_recv_msg(socket_fd,&msg,ssl,"",1) == 1) return 1;
	if (reg_compare (msg, "^\\+OK.*$") == 1){ //Verify answer from server
		cerr<<"ERROR: cannot connect to server or unrecongnized message."<<endl;
		return 1;
	}
	//STLS connection
	if (starttls_bool == 1){
		if (send_recv_msg(socket_fd,&msg,ssl,"STLS\r\n",2) == 1) return 1;
		if (reg_compare (msg, "^\\+OK.*$") == 1){ //Verify answer from server
			cerr<<"ERROR: cannot connect to server using STLS."<<endl;
			return 1;
		}
		if (init_ctx(ctx) == 1) return 1;
		if (connect_ssl(ctx, ssl,socket_fd, cert_file, cert_file_bool, cert_path, cert_path_bool) == 1) return 1 ;
		if (send_recv_msg(socket_fd,&msg,ssl,"",1) == 1) return 1;
	}
	return 0;
}

// Send and download communication POP3 messages
int send_recv_msg(int *socket_fd, string *msg, SSL* (&ssl), string sent_str, int sr_flag){
	ssize_t bytesrx;
	char buf[BUFFSIZE];
    bzero(buf, BUFFSIZE);
    msg->clear();
    struct pollfd fdinfo[1];
    int ret;

    fdinfo[0].fd = *socket_fd;
   	fdinfo[0].events = POLLIN|POLLPRI;

	if (sr_flag == 0 || sr_flag == 2){ //Only send message or receive&send message
		    if (ssl == NULL){
		    	if (send(*socket_fd,sent_str.c_str(),sent_str.size(),0) == -1){
					cerr<<"ERROR: cannot send."<<endl;
					return 1;
				}
		    } else SSL_write(ssl, sent_str.c_str(), strlen(sent_str.c_str()));
		}
	ret = poll(fdinfo,1,3000); //Poll in case of server not responding 
    if ((fdinfo[0].revents & (POLLIN | POLLPRI))){ 	
	 	if (sr_flag == 1 || sr_flag == 2){ //Only receive message or receive&send message
		 	if (ssl == NULL){
		 		bytesrx = read(*socket_fd, buf, BUFFSIZE);
		 	} else bytesrx = SSL_read(ssl, buf, BUFFSIZE);
		  	
		  	if (bytesrx < 0) {
		   		cerr<<"ERROR: Problem with receveing."<<endl;
		   		return 1;
		   	}
		   	msg->append(buf);
		}
	}
	return 0;
}

//Receive according to connection
ssize_t recv_type(int *socket_fd, SSL* (&ssl), char* buf){
	if (ssl == NULL) {
		return read(*socket_fd,buf,BUFFSIZE-1); //If non ssl connection
	} else return SSL_read(ssl,buf,BUFFSIZE-1); //If ssl connectio
}

//Receive email
int recv_in_loop(int *socket_fd, string *msg, SSL* (&ssl)){
	ssize_t bytesrx;
	char buf[BUFFSIZE];
    bzero(buf, BUFFSIZE);
 	msg->clear();
 	long long int i = 0;

	while ((bytesrx = recv_type(socket_fd,ssl, buf))  > 0){
		if(bytesrx < BUFFSIZE){
	   		buf[bytesrx] = '\0';
	 	} else buf[BUFFSIZE] = '\0';
		msg->append(buf);
		string str(buf,BUFFSIZE);
		if ((msg->find("\r\n.\r\n",0) != (string::npos)) || ((i == 0) && (msg->find("-ERR",0) != (string::npos)))) break; // Break loop, when ending CRLF.CRLF is found, end of message
		bzero(buf, BUFFSIZE);
		i++;
	}
	if (bytesrx < 0) {
   		cerr<<"ERROR: Problem with receveing."<<endl;
   		return 1;
   	}
	return 0;
}