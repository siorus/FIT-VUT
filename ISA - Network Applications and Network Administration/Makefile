CC = g++
CFLAGS = -std=c++11
POP = pop3_functions.cpp
COM = communication_functions.cpp
SSL = ssl_functions.cpp
AUX = aux_functions.cpp
all:	popcl

popcl:	popcl.cpp $(POP) $(COM) $(AUX) $(SSL)
	$(CC) $(CFLAGS) -o $@ popcl.cpp $(POP) $(COM) $(AUX) $(SSL) -lssl -lcrypto

clean:	
	rm -f *.o popcl