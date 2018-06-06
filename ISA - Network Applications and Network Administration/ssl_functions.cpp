/** Klient POP3 s podporou TLS
* @file ssl_functions.cpp
* @author Korček Juraj <xkorce01@stud.fit.vutbr.cz>
*/
#include "header.h"

//Init context
int init_ctx(SSL_CTX* (&ctx)){
    const SSL_METHOD *method;
 
    OpenSSL_add_all_algorithms();
    SSL_load_error_strings();   //Load err strings
    method = TLSv1_2_client_method(); //Set crypt method
	ctx = SSL_CTX_new (method);
    if ( ctx == NULL )
    {
        cerr<<"ERROR: creating context for SSL comunication."<<endl;
        return 1;
    }
    return 0;
}

//SSL connection
int connect_ssl(SSL_CTX* (&ctx), SSL* (&ssl), int* socket_fd, string cert_file, int cert_file_bool, string cert_path, int cert_path_bool){
    SSL_CTX *tst_ctx;
    X509 *cert;
    tst_ctx = SSL_CTX_new (TLSv1_2_client_method());

    if ((cert_file_bool == 0)&&(cert_path_bool == 0)) {
        SSL_CTX_set_default_verify_paths(ctx); //Load deafult path, if -c or -C is not set
    } else {
        if (cert_path_bool == 0){
            SSL_CTX_load_verify_locations(ctx, cert_file.c_str(),NULL); 
        } else if (cert_file_bool == 0){
            SSL_CTX_load_verify_locations(ctx, NULL,cert_path.c_str());
        } else {
            if (SSL_CTX_load_verify_locations(tst_ctx, cert_file.c_str(),cert_path.c_str()) == 0) {
                SSL_CTX_load_verify_locations(ctx, NULL,cert_path.c_str());
            } else SSL_CTX_load_verify_locations(ctx, cert_file.c_str(),NULL);
        }
    }
    SSL_CTX_free(tst_ctx);
    ssl = SSL_new(ctx);  //Create new connectio
    SSL_set_fd(ssl, *socket_fd); //Set file descriptor
    SSL_set_mode(ssl, SSL_MODE_AUTO_RETRY);
    
    if ( (SSL_connect(ssl)) == -1 ){   //Connect SSL
        cerr<<"ERROR: SSL connect was not succesful."<<endl;
        return 1;
    }
    if(((cert = SSL_get_peer_certificate(ssl)) != NULL) && (SSL_get_verify_result(ssl) != X509_V_OK)) //Test validity of certificate
    {
        cerr<<"ERROR: certificate is not valid or expired."<<endl;
        return 1;
    }
    X509_free(cert);

}