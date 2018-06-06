/** Klient POP3 s podporou TLS
* @file header.h
* @author Korček Juraj <xkorce01@stud.fit.vutbr.cz>
*/
#include <iostream>
#include <fstream>
#include <string.h>
#include <unistd.h>
#include <sys/socket.h>
#include <sys/time.h>
#include <sys/stat.h>
#include <arpa/inet.h>
#include <netdb.h>
#include <dirent.h>
#include <iomanip>
#include <regex.h>
#include <sys/types.h>
#include <getopt.h>
#include <sstream>
#include <openssl/bio.h>
#include <openssl/ssl.h>
#include <openssl/err.h>
#include <poll.h>
#include <ctype.h>
#include <algorithm>

#define BUFFSIZE 8192
using namespace std;

//STRUCT FOR GETOPT
typedef struct{
	string ip_addr;
	string auth_path;
	string out_dir;
	int port;
	string cert_file;
	string cert_path;
	bool auth_path_bool;
	bool out_dir_bool;
	bool port_bool;
	bool cert_file_bool;
	bool cert_path_bool;
	bool tls_bool;
	bool starttls_bool;
	bool delete_bool;
	bool new_bool;	
} Input_values;

/**
 * \brief Initialize ssl context
 * \param ctx SSL context
 * \return error code
*/
int init_ctx(SSL_CTX* (&ctx));

/**
 * \brief Initilize TLS connection
 * \param ctx SSL context
 * \param ssl SSL context
 * \param socket_fd Socket file descriptor
 * \param cert_file File with certificate
 * \param cert_file_bool Idicate usage of certfile
 * \param cert_path Directory with certificates
 * \param cert_file_bool Idicate usage of directory with certfiles
 * \return error code
*/
int connect_ssl(SSL_CTX* (&ctx), SSL* (&ssl), int* socket_fd, string cert_file, int cert_file_bool, string cert_path, int cert_path_bool);

/**
 * \brief Test whether directory exists
 * \param dir Path to dir
 * \return error code
*/
int test_dir(string dir);

/**
 * \brief Look for position of string inside of string
 * \param s Sting to search in
 * \param nth_pos NTH position of searching string inside s param
 * \param string_math Searching string
 * \param len Length of string_match  
 * \return error code
 */
int find_position(string s, int nth_pos, string match, int len);

/**
 * \brief Send given string and receive it
 * \param socket_fd Socket file descriptor
 * \param msg String where reveived message is stored
 * \param ssl SSL context
 * \param sent_str String which is wished to be send
 * \param sr_flag Flag indicates whether 0-send message only, 1-receive message only, 2-send&receive 
 * \return error code
*/
int send_recv_msg(int *socket_fd, string *msg, SSL* (&ssl), string sent_str, int sr_flag);

/**
 * \brief Decide wheteher use read or SSL_read
 * \param socket_fd Socket file descriptor
 * \param ssl SSL context
 * \param buf Buffer which stores received messages
 * \return error code
*/
ssize_t recv_type(int *socket_fd, SSL* (&ssl), char* buf);


/**
 * \brief Download e-mail
 * \param socket_fd Socket file descriptor
 * \param msg String where reveived message is stored
 * \param ssl SSL context
 * \return error code
*/
int recv_in_loop(int *socket_fd, string *msg, SSL* (&ssl));

/**
 * \brief Test regex presence in string
 * \param str String whre regex is searched for
 * \param reg_str Regex
 * \return error code
*/
int reg_compare (string str, string reg_str);

/**
 * \brief Parsing authorization file
 * \param file_name Name and path to auth file
 * \param user String where username will be stored
 * \param pass String where password will be stored
 * \return error code
*/
int authparse(const char *file_name, string *user, string *pass);

/**
 * \brief Create socket and connect to client
 * \param socket_fd Socket file descriptor
 * \param ip_addr IP address
 * \param port Port number
 * \param tls_bool TLS flag
 * \param starttls_bool STTL flag
 * \param ctx SSL context
 * \param ssl SSL context
 * \param cert_file File with certificate
 * \param cert_file_bool Idicate usage of certfile
 * \param cert_path Directory with certificates
 * \param cert_file_bool Idicate usage of directory with certfiles
 * \return error code
*/
int create_connect_socket (int *socket_fd, string ip_addr, int port, int tls_bool, int starttls_bool, SSL_CTX* (&ctx), SSL* (&ssl), string cert_file, int cert_file_bool, string cert_path, int cert_path_bool);

/**
 * \brief Authorization of user
 * \param socket_fd Socket file descriptor
 * \param auth_path Name and path to auth file
 * \param ssl SSL context
 * \return error code
*/
int auth_pop_server(int *socket_fd, string auth_path, SSL* (&ssl));

/**
 * \brief Authorization of user
 * \param socket_fd Socket file descriptor
 * \param num_of_messages Total number of messages on server
 * \param ssl SSL context
 * \return error code
*/
int list_msgs (int *socket_fd, long int *num_of_messages, SSL* (&ssl));

/**
 * \brief Erase of first line
 * \param msg Received message string
*/
void erase_first_line(string *msg);

/**
 * \brief Download UIDL list from server
 * \param socket_fd Socket file descriptor
 * \param num_of_messages Total number of messages on server
 * \param uid_email_tab Array of email IDs
 * \param ssl SSL context
 * \return error code
*/
int get_uid_list(int *socket_fd, int num_of_messages, string *uid_email_tab, SSL* (&ssl));

/**
 * \brief Delete message on server
 * \param socket_fd Socket file descriptor
 * \param message_no Number of message to delete
 * \param ssl SSL context
 * \return error code
*/
int delete_message(int *socket_fd, int message_no, SSL* (&ssl));

/**
 * \brief Flag messages which were already stored in PC
 * \param uid_email_tab Array of email IDs
 * \param out_dir Path to output directory
 * \param num_of_messages Total number of messages on server
 * \param num_of_marked_messages Total number of messages in PC
 * \param ssl SSL context
*/
void mark_new_messages(string *uid_email_tab, string out_dir, int num_of_messages, long int *num_of_marked_messages);

/**
 * \brief Download all e-mails
 * \param socket_fd Socket file descriptor
 * \param n Download only new messages
 * \param d Delete messages on server
 * \param out_dir Path to output directory
 * \param ssl SSL context
 * \return error code
*/
int download_messages (int *socket_fd, bool n, bool d, string out_dir, SSL* (&ssl));

/**
* \brief Prints help message
*/
void help_text(void);

/**
* \brief Find biggest filename number
* \param directory path
* \return max filename number
*/
int max_file_num(string out_dir);