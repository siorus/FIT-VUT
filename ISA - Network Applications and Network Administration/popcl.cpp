/** Klient POP3 s podporou TLS
* @file popcl.cpp
* @author Korček Juraj <xkorce01@stud.fit.vutbr.cz>
*/
#include "header.h"

int main (int argc, char *argv[]){
	int socket_fd,ch;
	Input_values input;
	string msg;
	SSL_CTX* ctx = NULL;
  	SSL* ssl = NULL;


	SSL_library_init(); //INIT SSL LIBRARY
	input.new_bool = 0;
	input.delete_bool = 0;
	input.port_bool = 0;
	input.tls_bool = 0;
	input.starttls_bool = 0;
	input.auth_path_bool = 0;
	input.out_dir_bool = 0;
	input.cert_file_bool = 0;
	input.cert_path_bool = 0;
	while ((ch = getopt(argc,argv, "a:o:p:c:C:TSdnh")) != -1) {
	switch(ch){
		case 'a':
			input.auth_path = (optarg);
			input.auth_path_bool = 1;
			break;
		case 'o':
			input.out_dir = (optarg);
			input.out_dir_bool = 1;
			break;
		case 'p':
			if(reg_compare(optarg,"^[0-9]+$") == 0){
				input.port = stoi(optarg);
				input.port_bool = 1;
			} else {
				cerr<<"ERROR: given port is not a number."<<endl;
				return -1;
			}
			break;
		case 'T':
			input.tls_bool = 1;
			break;
		case 'S':
			input.starttls_bool = 1;
			break;
		case 'c':
			input.cert_file = (optarg);
			input.cert_file_bool = 1;
			break;
		case 'C':
			input.cert_path = (optarg);
			input.cert_path_bool = 1;
			break;
		case 'd':
			input.delete_bool = 1;
			break;
		case 'n':
			input.new_bool = 1;
			break;
		case 'h':
			help_text();
			return 0;
			break;
		default:
    		//help_text();
    		return -1;
			break;
		}
	}

	if (optind+1 < argc) { //LOAD SERVER IP/HOSTAME - VARIABLE POSITION, NO ARGUMENT
		cerr<<"ERROR: too many servers."<<endl;
    	//cerr<<""<<endl;
    	//help_text();
    	return -1; 
    } else if (optind+1 > argc) {
    	cerr<<"ERROR: server not specified."<<endl;
    	//cerr<<""<<endl;
    	//help_text();
    	return -1;

    } else 	input.ip_addr = argv[optind];
  
	if ((argc < 4)||(input.auth_path_bool == 0)||(input.out_dir_bool == 0)){ //AT LEAST 4 ARGS NEEDED
		cerr<<"ERROR: too few arguments."<<endl;
		//cerr<<""<<endl;
    	//help_text();
		return -1;
	}
	if ((input.port_bool == 0) && (input.tls_bool == 1)){ //DEFAULT PORT ASSIGN
		input.port = 995;
	} else if ((input.port_bool == 0)){
		input.port = 110;
	}
	if ((input.cert_path_bool == 1) || (input.cert_file_bool == 1)){ //CERT FILE OR PARH CAN BE SELECTED ONLY WITH T OR S
		if ((input.tls_bool == 0) && (input.starttls_bool == 0)){
			cerr<<"ERROR: cert path specified without type of cipher."<<endl;
			//cerr<<""<<endl;
    		//help_text();
			return -1;
		}
	}
	if ((input.starttls_bool == 1) && (input.tls_bool == 1)){
		cerr<<"ERROR: unsupported combination of cipher arguments."<<endl;
		//cerr<<""<<endl;
    	//help_text();
		return -1;
	}

	if (test_dir(input.out_dir) == 1){
		cerr<<"ERROR: cannot access output directory."<<endl;
		return -1;
	} else if (test_dir(input.out_dir) == 2){
		cerr<<"ERROR: not a directory."<<endl;
		return -1;
	}
	
	//RESOLVE HOSTNAME IP, CONNECT TO SERVER AND ESTABLISH SECURE CONNECTION
	if((create_connect_socket(&socket_fd, input.ip_addr, input.port, input.tls_bool, input.starttls_bool,ctx, ssl, input.cert_file, input.cert_file_bool, input.cert_path, input.cert_path_bool)) == 1) return -1;
	
	//AUTHORIZE POP3 USER
	if (auth_pop_server(&socket_fd,input.auth_path, ssl) == 1) return -1;

	//DOWNLOAD ALL/NEW MESSAGES
	if (download_messages(&socket_fd,input.new_bool,input.delete_bool, input.out_dir, ssl) == 1) return -1;
	
	//SEND QUIT COMMAND TO CLOSE COMMUNICATION
	if (send_recv_msg(&socket_fd,&msg,ssl,"QUIT \r\n",2) == 1){
		cerr<<"ERROR: quiting connection."<<endl;
		return 1;
	}
	//close(socket_fd);
	if (ssl != NULL) SSL_shutdown (ssl);
	close (socket_fd);
	//shutdown(socket_fd,SHUT_WR);

	//shutdown(socket_fd,SHUT_RD);
	//close(socket_fd);
	if (ssl != NULL ){
	SSL_free(ssl);
	SSL_CTX_free(ctx);
	}
	//close(socket_fd);
	//shutdown(socket_fd,SHUT_WR);
	
}