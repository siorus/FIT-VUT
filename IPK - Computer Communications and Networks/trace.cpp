// IPK Projekt2 - Treceroute
// Author: Juraj Korcek, xkorce01
#include <iostream>
#include <sys/socket.h>
#include <sys/time.h>
#include <netdb.h>
#include <arpa/inet.h>
#include <linux/errqueue.h>
#include <unistd.h>
#include <string.h>
#include <iomanip>

#define BUFF_SIZE 2048

using namespace std;

typedef struct{
	int frst_ttl;
	int max_ttl;
	string ip_addr;
} Input_values;

//Get types and codes of ICMP and return value related to codes
int ipv4_examine_print(int ttl, double delay, struct sock_extended_err *err_sock) {
	//int type,code;
	char rec_src_addr[255];
	char host[1025];
	string space = "  ";
	sockaddr_in *rec_sock_addr;
	sockaddr_in name;

	rec_sock_addr = (struct sockaddr_in*)(err_sock+1); //Prepare member of structure where IP is located
	bzero(&name,sizeof(name));
	name.sin_family = AF_INET;
	name.sin_addr.s_addr = rec_sock_addr->sin_addr.s_addr;
	inet_ntop(AF_INET, &rec_sock_addr->sin_addr, rec_src_addr, 255); // Resolve IP address

	if((getnameinfo((sockaddr*)&name, sizeof(struct sockaddr_in), host, sizeof(host), NULL, 0, NI_NAMEREQD)) != 0) {
		//bzero(host,1024);
		strncpy(host,rec_src_addr,255);
		space = "  ";
	}	

	switch ((int)err_sock->ee_type) {
		case 3: {
			switch ((int)err_sock->ee_code) {
				case 0: { //NETWORK UNREACHABLE
			    	cout<<ttl<<space<<host<<"  ("<<rec_src_addr<<")  "<<"N!"<<endl;
					return 3;
				}
				case 1: { //HOST UNREACHABLE
					cout<<ttl<<space<<host<<"  ("<<rec_src_addr<<")  "<<"H!"<<endl;
					return 3;
				}
				case 2: { //PROTOCOL UNREACHABLE
					cout<<ttl<<space<<host<<"  ("<<rec_src_addr<<")  "<<"P!"<<endl;
					return 3;
				}
				case 3: { //PORT UNREACHABLE
					cout<<std::fixed<<std::setprecision( 3 )<<ttl<<space<<host<<"  ("<<rec_src_addr<<")  "<<delay<<" ms"<<endl;
					return 0;
				}
				case 13: { //COMMUNICATION ADMINISTRATIVELY PROHIBIED
					cout<<ttl<<space<<host<<"  ("<<rec_src_addr<<")  "<<"X!"<<endl;
					return 3;
				}
				default: {
					return 4; //NOT RECOGNIZED MESSAGE
					break;
				}
			}
		}
		case 11: {
			if (((int)err_sock->ee_code) == 0) {
				cout<<std::fixed<<std::setprecision( 3 )<<ttl<<space<<host<<"  ("<<rec_src_addr<<")  "<<delay<<" ms"<<endl; //TIME EXCEEDED
				return 2;
			}
			break;
		}
		default: {
			return 4; //NOT RECOGNIZED MESSAGE
			break;
		}
	}

return 4; //RECEIVED MESSAGES DOES NOT MATCH
}

//Get types and codes of ICMP and return value related to codes
int ipv6_examine_print(int ttl, double delay, struct sock_extended_err *err_sock) {
	char rec_src_addr[255];
	char host[1025];
	string space = "  ";
	sockaddr_in6 *rec_sock_addr_6;
	sockaddr_in6 name;

	rec_sock_addr_6 = (struct sockaddr_in6*)(err_sock+1); //Prepare member of structure where IP is located
	inet_ntop(AF_INET6, &rec_sock_addr_6->sin6_addr, rec_src_addr, 255); // Resolve IP address

	bzero(&name,sizeof(name));
	name.sin6_family = AF_INET6;
	memcpy(name.sin6_addr.s6_addr, rec_sock_addr_6->sin6_addr.s6_addr, sizeof (rec_sock_addr_6->sin6_addr.s6_addr));
	//name.sin6_addr.s6_addr = rec_sock_addr_6->sin6_addr.s6_addr;

	//int a = getnameinfo((sockaddr*)&name, sizeof(struct sockaddr_in6), host, sizeof(host), NULL, 0, NI_NAMEREQD);
	if((getnameinfo((sockaddr*)&name, sizeof(struct sockaddr_in6), host, sizeof(host), NULL, 0, NI_NAMEREQD)) != 0) {
		//bzero(host,1024);
		strncpy(host,rec_src_addr,255);
		space = "  ";
	}
	
	switch ((int)err_sock->ee_type) {
		case 1: {
			switch ((int)err_sock->ee_code) {
				case 0: { //NETWORK UNREACHABLE
			    	cout<<ttl<<space<<host<<"  ("<<rec_src_addr<<")  "<<"N!"<<endl;
					return 3;
				}
				//OK
				case 1: { //COMMUNICATION ADMINISTRATIVELY PROHIBIED
					cout<<ttl<<space<<host<<"  ("<<rec_src_addr<<")  "<<"X!"<<endl;
					return 3;
				}
				case 3: { //HOST UNREACHABLE
					cout<<ttl<<space<<host<<"  ("<<rec_src_addr<<")  "<<"H!"<<endl;
					return 3;
				}
				case 4: { //PORT UNREACHABLE
					cout<<std::fixed<<std::setprecision( 3 )<<ttl<<space<<host<<"  ("<<rec_src_addr<<")  "<<delay<<" ms"<<endl;
					return 0;
				}
				default: {
					return 4; //NOT RECOGNIZED MESSAGE
				}
			}
		}
		//OK
		case 3: {
			if (((int)err_sock->ee_code) == 0) {
				cout<<std::fixed<<std::setprecision( 3 )<<ttl<<space<<host<<"  ("<<rec_src_addr<<")  "<<delay<<" ms"<<endl;
				return 2;
			 }
			break;
		}
		case 4: { //PROTOCOL UNREACHABLE
			if ((int)err_sock->ee_code == 1) {
				cout<<ttl<<space<<host<<"  ("<<rec_src_addr<<")  "<<"P!"<<endl;
				return 3;
			}
			break;
		}
		default: {
			return 4; //NOT RECOGNIZED MESSAGE
			break;
		}
	}
	

return 4; //RECEIVED MESSAGES DOES NOT MATCH
}

void ipv4_trace(Input_values input, addrinfo *result, int port) {
	int sock_trace,ttl,max_ttl,sel,ret_ipv4_examine,on;
	struct msghdr msg;
	struct cmsghdr *cmsg;
	struct iovec  io;
	struct sock_extended_err *err_sock;
	sockaddr_in send_sock_addr;
	timeval timeout,rec_time,send_time;
	fd_set trace_set;
	double delay;
	char buffer[BUFF_SIZE];
	char rec_src_addr[255];

	if ((sock_trace = socket(AF_INET,SOCK_DGRAM,0)) == -1) {
		cerr<<"ERROR CREATING SOCKET"<<endl;
		exit(-1);
	}


	memset(&buffer,0,BUFF_SIZE);
	memset(&msg, 0, sizeof(msg));

	ttl = input.frst_ttl;
	max_ttl = input.max_ttl;

	//send_sock_addr.sin_family = AF_INET;

  	memset(&send_sock_addr, 0, sizeof(sockaddr_in));
  	memcpy(&(send_sock_addr), result->ai_addr, result->ai_addrlen);

	on = IP_PMTUDISC_DO;
	if (setsockopt(sock_trace, SOL_IP, IP_MTU_DISCOVER, (const char *)&on, sizeof(on))) { //Do not fragment this msg
		cerr<<"ERROR SETSOCKOPT"<<endl;
		exit(1);	
	}
	on = 1;
	if (setsockopt(sock_trace, SOL_IP, IP_RECVERR, (const char *)&on, sizeof(on))) { //Receve error in this socket
		cerr<<"ERROR SETSOCKOPT"<<endl;
		exit(1);	
	}			
	/*if (setsockopt(sock_trace, SOL_IP, IP_RECVTTL, (const char *)&on, sizeof(on))) {
		cerr<<"ERROR SETSOCKOPT"<<endl;
		exit(1);
	}
	*/
	
	do {
		bzero(buffer,BUFF_SIZE);
		bzero(rec_src_addr,255);
		memset(&msg, 0, sizeof(msg));
		err_sock = NULL;

		setsockopt(sock_trace, SOL_IP, IP_TTL, (const char *)&ttl, sizeof(ttl)); //Set ttl every iteration

		(port == 33534) ? port = 33434: port++; //Change ports due to some of them could be open, which can cause stuck
		send_sock_addr.sin_port = htons(port);

		if (sendto(sock_trace, NULL, 0, 0, (sockaddr *)&send_sock_addr, sizeof(send_sock_addr)) == -1) {
			if (sendto(sock_trace, NULL, 0, 0, (sockaddr *)&send_sock_addr, sizeof(send_sock_addr)) == -1) {
				cerr<<"ERROR SENDING"<<endl;
				exit(-1);
			}
		}

		timeout.tv_sec = 2; //Default timeout 2s is not enough, I was forced to used it
		timeout.tv_usec = 0;
		FD_ZERO(&trace_set);
		FD_SET(sock_trace,&trace_set);
		gettimeofday(&send_time,NULL);

		while(1) {
			if ((sel = select(sizeof(trace_set),&trace_set,NULL,NULL,&timeout)) == -1) {
				cerr<<"ERROR SELECT"<<endl;
				exit(-1);
			} else if (sel > 0 ) {
				char control[1024];
				io.iov_base = buffer;
				io.iov_len = sizeof(buffer);
				memset(&msg, 0, sizeof(msg));
				msg.msg_iov = &io;
				msg.msg_iovlen = 1;
				msg.msg_control = &control;
				msg.msg_controllen = sizeof(control);

				if ((recvmsg(sock_trace, &msg, MSG_ERRQUEUE)) == -1) {
	 					cerr << "ERROR RECEIVING"<<endl;
	 					exit(-1);
				}

				gettimeofday(&rec_time,NULL);
				delay = ((rec_time.tv_sec - send_time.tv_sec)*1000.0 + (rec_time.tv_usec-send_time.tv_usec)/1000.0); //RTT in miliseconds

				//Cycle for catching error msgs
				for (cmsg = CMSG_FIRSTHDR(&msg); cmsg != NULL; cmsg = CMSG_NXTHDR(&msg, cmsg)) {
					if (cmsg->cmsg_level == SOL_IP && cmsg->cmsg_type == IP_RECVERR) {
		    			err_sock = (struct sock_extended_err *) CMSG_DATA(cmsg);
		    		}
	    		}

		    	ret_ipv4_examine = ipv4_examine_print(ttl,delay,err_sock);
		    	//if (ret_ipv4_examine == 0) {
					break;
				//} else if (ret_ipv4_examine == 1) {
				//	continue;
				//} else {
				//	break;
				//}
			} else {
				cout<<ttl<<"  *"<<endl; //Tiomeout
				break;	
			}
		}
		ttl++;
		if ((ret_ipv4_examine == 0)||(ret_ipv4_examine == 3)||(ret_ipv4_examine == 4)) break;		
	} while((ttl < max_ttl+1) && (ret_ipv4_examine == 2)); //Continue only if msg is time exceeded
	close(sock_trace);
	freeaddrinfo(result);
}

void ipv6_trace(Input_values input, addrinfo *result, int port) {
	int sock_trace,ttl,max_ttl,sel,ret_ipv6_examine,on;
	struct msghdr msg;
	struct cmsghdr *cmsg;
	struct iovec  io;
	struct sock_extended_err *err_sock;
	sockaddr_in6 send_sock_addr_6;
	timeval timeout,rec_time,send_time;
	fd_set trace_set;
	double delay;
	char buffer[BUFF_SIZE];
	char rec_src_addr[255];

	if ((sock_trace = socket(AF_INET6,SOCK_DGRAM,0)) == -1) {
				cerr<<"ERROR CREATING SOCKET"<<endl;
				exit(-1);
	}


	memset(&buffer,0,BUFF_SIZE);
	memset(&msg, 0, sizeof(msg));

	ttl = input.frst_ttl;
	max_ttl = input.max_ttl;

	//send_sock_addr.sin_family = AF_INET;

  	memset(&send_sock_addr_6, 0, sizeof(sockaddr_in6));
  	memcpy(&(send_sock_addr_6), result->ai_addr, result->ai_addrlen);

	on = IP_PMTUDISC_DO;
	if (setsockopt(sock_trace, IPPROTO_IPV6, IPV6_MTU_DISCOVER, (const char *)&on, sizeof(on))) { //Do not fragment this msg
		cerr<<"ERROR SETSOCKOPT MTU"<<endl;
		exit(1);	
	}
	on = 1;
	if (setsockopt(sock_trace, IPPROTO_IPV6, IPV6_RECVERR, (const char *)&on, sizeof(on))) { //Receve error in this socket
		cerr<<"ERROR SETSOCKOPT RECERR"<<endl;
		exit(1);	
	}
	/*if (setsockopt(sock_trace, SOL_IPV6, IPV6_HOPLIMIT, (const char *)&on, sizeof(on))) {
		cerr<<"ERROR SETSOCKOPT HOPLIM"<<endl;
		exit(1);
	}*/

	do {
		bzero(buffer,BUFF_SIZE);
		bzero(rec_src_addr,255);
		memset(&msg, 0, sizeof(msg));
		err_sock = NULL;

		setsockopt(sock_trace, IPPROTO_IPV6, IPV6_UNICAST_HOPS, (const char *)&ttl, sizeof(ttl)); //Set ttl every iteration

		(port == 33534) ? port = 33434: port++; //Change ports due to some of them could be open, which can cause stuck
		send_sock_addr_6.sin6_port = htons(port);

		if (sendto(sock_trace, NULL, 0, 0, (sockaddr *)&send_sock_addr_6, sizeof(send_sock_addr_6)) == -1) {
			if (sendto(sock_trace, NULL, 0, 0, (sockaddr *)&send_sock_addr_6, sizeof(send_sock_addr_6)) == -1) {
				cerr<<"ERROR SENDING"<<endl;
				exit(-1);
			}
		}

		timeout.tv_sec = 2; //Default timeout 2s is not enough, I was forced to used it
		timeout.tv_usec = 0;
		FD_ZERO(&trace_set);
		FD_SET(sock_trace,&trace_set);
		gettimeofday(&send_time,NULL);

		while(1) {
			if ((sel = select(sizeof(trace_set),&trace_set,NULL,NULL,&timeout)) == -1) {
				cerr<<"ERROR SELECT"<<endl;
				exit(-1);
			} else if (sel > 0 ) {
				char control[1024];
				io.iov_base = buffer;
				io.iov_len = sizeof(buffer);
				memset(&msg, 0, sizeof(msg));
				msg.msg_iov = &io;
				msg.msg_iovlen = 1;
				msg.msg_control = &control;
				msg.msg_controllen = sizeof(control);

				if ((recvmsg(sock_trace, &msg, MSG_ERRQUEUE)) == -1) {
	 					cerr << "ERROR RECEIVING"<<endl;
	 					exit(-1);
				}

				gettimeofday(&rec_time,NULL);
				delay = ((rec_time.tv_sec - send_time.tv_sec)*1000.0 + (rec_time.tv_usec-send_time.tv_usec)/1000.0); //RTT in miliseconds

				//Cycle for catching error msgs
				for (cmsg = CMSG_FIRSTHDR(&msg); cmsg != NULL; cmsg = CMSG_NXTHDR(&msg, cmsg)) {
					if (cmsg->cmsg_level == IPPROTO_IPV6 && cmsg->cmsg_type == IPV6_RECVERR) {
		    			err_sock = (struct sock_extended_err *) CMSG_DATA(cmsg);
		    		}
		    	}
		    	
		    	ret_ipv6_examine = ipv6_examine_print(ttl,delay,err_sock);
		    	//if (ret_ipv6_examine == 0) {
					break;
				//} else if (ret_ipv6_examine == 1) {
				//	continue;
				//} else {
				//	break;
				//}
			} else {
				cout<<ttl<<"  *"<<endl; //Timeout
				break;	
			}
		}
		ttl++;
		if ((ret_ipv6_examine == 0)||(ret_ipv6_examine == 3)||(ret_ipv6_examine == 4)) break;		
	} while((ttl < max_ttl+1) && (ret_ipv6_examine == 2)); //Continue only if msg is time exceeded
	close(sock_trace);
	freeaddrinfo(result);
}


int main (int argc, char *argv[]){
int ch,max_ttl_bool,frst_ttl_bool;
Input_values input;
struct addrinfo hints; //Structure for input of getaddrinfo
struct addrinfo *result; //Structure for result of getaddrinfo

frst_ttl_bool = 0;
max_ttl_bool = 0;

while ((ch = getopt(argc,argv, "f:m:")) != -1) {
	switch(ch){
		case 'f':
			input.frst_ttl = std::stoi(optarg);
			frst_ttl_bool = 1;
			break;
		case 'm':
			input.max_ttl = std::stoi(optarg);
			max_ttl_bool = 1;
			break;
		default:
			break;
	}
}

if (!frst_ttl_bool) {
	input.frst_ttl = 1;
}
if (!max_ttl_bool) {
	input.max_ttl = 30;
}

if (frst_ttl_bool && max_ttl_bool && argc == 6) {
	input.ip_addr = argv[5];
} else if ((frst_ttl_bool || max_ttl_bool) && argc == 4) {
	input.ip_addr = argv[3];
} else if (argc == 2) {
	input.ip_addr = argv[1];
} else {
	cerr<<"ARGUMENT ERROR"<<endl;
	return -1;
}

memset(&hints,0,sizeof(struct addrinfo));
hints.ai_flags=AI_ADDRCONFIG;
hints.ai_family = AF_UNSPEC;
hints.ai_socktype = SOCK_DGRAM;
hints.ai_protocol=0;
int port = 33434;

//Main reason is to resolve whether give IP is v4 or v6
if (getaddrinfo(input.ip_addr.c_str(),(to_string(port)).c_str(),&hints, &result) != 0) {
		cerr<<"Problem resolving ip address"<<endl;
		return -1;
}

if (result->ai_family == AF_INET) {
	ipv4_trace(input,result,port);
} else if (result->ai_family == AF_INET6) {
	ipv6_trace(input,result,port);
} else {
	cerr<<"Unrecognized address"<<endl;
	return -1;
}

return 0;
}