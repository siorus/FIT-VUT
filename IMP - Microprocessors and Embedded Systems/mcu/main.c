/*******************************************************************************
   main.c: Morse encoder 100% - original
   Author: Juraj Korček, xkorce01 <xkorce01 AT stud.fit.vutbr.cz>
   Last modified: 16.12.2017
*******************************************************************************/

#include <fitkitlib.h>
#include <keyboard/keyboard.h>
#include <lcd/display.h>

#define true 1
#define false 0

short kbrd_timer_en;
int kbrd_timer;
short first_char;
int beep_signals[11];
short beep; //Beeping in progress bool
char new_letter = 1;
int beep_char_pos;
short beep_dot = 0x180; //Beep length of dot 
short beep_dash = 0x480; //Bepp length of dash

struct circBuf_t {
    char arr_of_chars[65];
    short head;
    short tail;
    short curLen;
    short maxLen;
} buff;

char prev_char;
char last_ch;
char char_cnt;

const char kbrd_AlphaBet[8][6] = {
  "ABC2", //button 2
  "DEF3", //button 3
  "GHI4", //button 4
  "JKL5", //button 5
  "MNO6", //button 6
  "PQRS7", //button 7
  "TUV8", //button 8
  "WXYZ9", //button 9
};

char AlphaBet_Morse[26][6] = {
  ".-",   //A
  "-...", //B
  "-.-.", //C
  "-..",  //D
  ".",    //E
  "..-.", //F
  "--.",  //G
  "....", //H
  "..",   //I
  ".---", //J
  "-.-",  //K
  ".-..", //L
  "--",   //M
  "-.",   //N
  "---",  //O
  ".--.", //P
  "--.-", //Q
  ".-.",  //R
  "...",  //S
  "-",    //T
  "..-",  //U
  "...-", //V
  ".--",  //W
  "-..-", //X
  "-.--", //Y
  "--.."  //Z
};

char Num_Morse[10][6] = {
  "-----", //0
  ".----", //1
  "..---", //2
  "...--", //3
  "....-", //4
  ".....", //5
  "-....", //6
  "--...", //7
  "---..", //8
  "----."  //9
};

/*******************************************************************************
 * Decode sequence of pushed numbers on keybopard to alphabet
*******************************************************************************/
char decode_kbrd(){
  return kbrd_AlphaBet[prev_char - '2'][char_cnt-1];
}

/*******************************************************************************
 * Decode alphabet char to dots and dashes
*******************************************************************************/
char* decode_morse(char c) {
  if ((c >= 'A') && (c <= 'Z')){
    return AlphaBet_Morse[c - 'A'];
  } else if ((c >= '0') && (c <= '9')) return Num_Morse[c - '0'];
  return '\0';
}

/*******************************************************************************
 * Output for QDevKit terminal
*******************************************************************************/
void term_output(char ch,char *str){
  term_send_crlf();
  term_send_str(str);
  term_send_char('\'');
  term_send_char(ch);
  term_send_char('\'');
  term_send_crlf();
  term_send_str(" >");
}

/*******************************************************************************
 * Print help
*******************************************************************************/
void print_user_help(void)
{
}

/*******************************************************************************
 * Decode and execution of user commands
*******************************************************************************/
unsigned char decode_user_cmd(char *cmd_ucase, char *cmd)
{
  return CMD_UNKNOWN;
}

/*******************************************************************************
 * Output for processing char to LCD and 
*******************************************************************************/
void print_processing_char(){
    LCD_clear();
    LCD_append_char(buff.arr_of_chars[buff.tail]);
    LCD_append_char(' ');
    LCD_append_string(decode_morse(buff.arr_of_chars[buff.tail]));
}

/*******************************************************************************
 * Interupt of Timer A
*******************************************************************************/
interrupt (TIMERA0_VECTOR) beep_timer(void){
  CCTL0 &= ~CCIE; //Stop timer interupt
  beep_char_pos++;
  beep = 0;
}

/*******************************************************************************
 * Push to circular buffer
*******************************************************************************/
void buf_push(char letter) {
    int next = buff.head + 1;
    if (next >= buff.maxLen) next = 0;

    if (next != buff.tail){  //Not full buffer
      buff.arr_of_chars[buff.head] = letter; //Push letter
      buff.head = next; //Move head to next offset.
      (buff.curLen)++;
      term_output(buff.arr_of_chars[buff.tail+buff.curLen-1],"Entered letter: ");
    } else term_output(buff.arr_of_chars[buff.tail+buff.curLen-1],"Full buffer, skipping input, wait a while");
}

/*******************************************************************************
 * Pop from circular buffer
*******************************************************************************/
char buf_pop() {
    int next = buff.tail + 1;
    if(next >= buff.maxLen) next = 0;

    print_processing_char();
    char ret = buff.arr_of_chars[buff.tail];
    buff.tail = next; //Move tail to next offset
    (buff.curLen)--;
    return ret; //Pop letter
}

/*******************************************************************************
 * Create beeps period from dots and dashes 
*******************************************************************************/
void map_morse_to_signals(){
  int j;
  for (j = 0; j < 11; j++){
    beep_signals[j]= 0;
  }
  char *c = decode_morse(buf_pop());
  int i = 0;
  int k = 0;
  while ( c[k] != 0){
    if (c[k] == '.'){
      beep_signals[i] = beep_dot;
    } else if (c[k] == '-'){
      beep_signals[i] = beep_dash;
    }
    i++;
    if (c[k+1] == 0) { //End of morse word, ending delay between alphabet chars
      beep_signals[i] = beep_dash;
    } else {
      beep_signals[i] = beep_dot; //Space between dots and dashes in char
    }
    i++;
    k++;
  }
}

/*******************************************************************************
 * Auxiliary function for counting nums of pushes of buttons
*******************************************************************************/
void num_of_push(char ch) {
  if (first_char == 1){
    first_char = 0;
    prev_char = ch;
    char_cnt++;
  } else if (prev_char == ch){ //Same button was pressed
    char_cnt++;
    term_output(char_cnt+'0',"Num of repetition of current button: ");
  } else if (!((prev_char == '0')||(prev_char == '1'))) { //New button was pressed, not 0 or 1
    if (prev_char != 'Z') buf_push(decode_kbrd());
    char_cnt = 1;
    prev_char = ch;
  } else { //0 or 1 button was pressed
    char_cnt = 1;
    prev_char = ch;
  }
}

/*******************************************************************************
 * Function for handle each buttton operation
*******************************************************************************/
int keyboard_operation()
{
  char ch;
  ch = key_decode(read_word_keyboard_4x4());
  if (ch != last_ch) 
  {
    last_ch = ch;
    if (ch != 0) 
    {
      switch (ch) {
        case '0':
        case '1':
          kbrd_timer_en = 0; //Stop keyboard timer
          if (!((prev_char ==  '0') || (prev_char ==  '1'))) {
             if (prev_char != 'Z') buf_push(decode_kbrd());
          }
          buf_push(ch);
          prev_char = ch;
          char_cnt = 1;
          break;
        case '2':
        case '3':
        case '4':
        case '5':
        case '6':
        case '7':
        case '8':
        case '9':
          kbrd_timer = TAR + 0x1800; //Keyboard timeout
          kbrd_timer_en = 1; //Start keyboard timer
          num_of_push(ch);
          break;
        case '*':
        case '#':
          return 0;
        case 'A':
          beep_dot = 0x180;
          beep_dash = 0x480;
          return 0;
        case 'B':
          beep_dot = 0x180*1.5;
          beep_dash = 0x480*1.5;
          return 0;
        case 'C':
          beep_dot = 0x180*2;
          beep_dash = 0x480*2;
          return 0;
        case 'D':
          return 0;
      }
      if ((char_cnt > 4) && (((ch >= '2') && (ch <= '6')) || (ch == '8'))){
         char_cnt = 1; //Zeroing cnt of current button, e.g.  button 2 can be pressed 5 times then 'A' will be output      
       } else if (char_cnt > 5) char_cnt = 1;
    }
  }
  return 0;
}

/*******************************************************************************
 * Init FPGA components
*******************************************************************************/
void fpga_initialized()
{
  LCD_init();
  LCD_clear();
  LCD_append_string("Morse Encoder");
}

int main(void)
{
  buff.head = 0;
  buff.tail = 0;
  buff.maxLen = 65;
  buff.curLen = 0;

  prev_char = 'Z';
  first_char = 1;
  beep = 0;
  beep_char_pos = 0;
  char_cnt = 0;
  last_ch = 0;
 
  initialize_hardware();
  keyboard_init();
  LCD_send_cmd(LCD_DISPLAY_ON_OFF | LCD_DISPLAY_ON | LCD_CURSOR_OFF, 0);
  
  WDG_stop();
  TACTL = TASSEL_1 + ID_3 + MC_2;
  P4DIR |= 0x010; //P4M4
  P4OUT &= ~0x010;

  while (true) {
    if ((TAR>=kbrd_timer)&&kbrd_timer_en){
      kbrd_timer_en = 0; //Stop keyboardard timer
      buf_push(decode_kbrd(prev_char));
      prev_char = 'Z'; //In case same button was pressed after timeout
    }
    if ((!beep) && ((buff.tail != buff.head) || (beep_char_pos != 0))){
      if (new_letter){
        new_letter = 0;
        map_morse_to_signals();
      }
      if (beep_signals[beep_char_pos]) {
        beep = 1;
        CCR0 = TAR + beep_signals[beep_char_pos]; //Time of beep
        CCTL0 = CCIE; //Start timer interupt
        P4OUT ^= 0x010; //Flip buzzer
       } else {
        new_letter = 1;
        beep_char_pos = 0;
      }
    }
    keyboard_operation();
    terminal_idle();
  }         
}
