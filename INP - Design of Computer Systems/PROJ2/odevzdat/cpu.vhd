-- cpu.vhd: Simple 8-bit CPU (BrainLove interpreter)
-- Copyright (C) 2016 Brno University of Technology,
--                    Faculty of Information Technology
-- Author(s): xkorce01, Juraj Korcek
--

library ieee;
use ieee.std_logic_1164.all;
use ieee.std_logic_arith.all;
use ieee.std_logic_unsigned.all;

-- ----------------------------------------------------------------------------
--                        Entity declaration
-- ----------------------------------------------------------------------------
entity cpu is
 port (
   CLK   : in std_logic;  -- hodinovy signal
   RESET : in std_logic;  -- asynchronni reset procesoru
   EN    : in std_logic;  -- povoleni cinnosti procesoru
 
   -- synchronni pamet ROM
   CODE_ADDR : out std_logic_vector(11 downto 0); -- adresa do pameti
   CODE_DATA : in std_logic_vector(7 downto 0);   -- CODE_DATA <- rom[CODE_ADDR] pokud CODE_EN='1'
   CODE_EN   : out std_logic;                     -- povoleni cinnosti
   
   -- synchronni pamet RAM
   DATA_ADDR  : out std_logic_vector(9 downto 0); -- adresa do pameti
   DATA_WDATA : out std_logic_vector(7 downto 0); -- mem[DATA_ADDR] <- DATA_WDATA pokud DATA_EN='1'
   DATA_RDATA : in std_logic_vector(7 downto 0);  -- DATA_RDATA <- ram[DATA_ADDR] pokud DATA_EN='1'
   DATA_RDWR  : out std_logic;                    -- cteni (1) / zapis (0)
   DATA_EN    : out std_logic;                    -- povoleni cinnosti
   
   -- vstupni port
   IN_DATA   : in std_logic_vector(7 downto 0);   -- IN_DATA <- stav klavesnice pokud IN_VLD='1' a IN_REQ='1'
   IN_VLD    : in std_logic;                      -- data platna
   IN_REQ    : out std_logic;                     -- pozadavek na vstup data
   
   -- vystupni port
   OUT_DATA : out  std_logic_vector(7 downto 0);  -- zapisovana data
   OUT_BUSY : in std_logic;                       -- LCD je zaneprazdnen (1), nelze zapisovat
   OUT_WE   : out std_logic                       -- LCD <- OUT_DATA pokud OUT_WE='1' a OUT_BUSY='0'
 );
end cpu;


-- ----------------------------------------------------------------------------
--                      Architecture declaration
-- ----------------------------------------------------------------------------
architecture behavioral of cpu is

	signal pc_inc : std_logic;
	signal pc_dec : std_logic;
	signal pc_out : std_logic_vector(11 downto 0) := (others => '0');

	signal cnt_inc : std_logic;
	signal cnt_dec : std_logic;
	signal cnt_out : std_logic_vector(7 downto 0) := (others => '0');
	signal cnt_bit : std_logic;

	signal ptr_inc : std_logic;
	signal ptr_dec : std_logic;
	signal ptr_data : std_logic_vector(9 downto 0) := (others => '0');

	type instr is (INCPTR,DECPTR,INCPTRDATA,DECPTRDATA,LOOPSTART,LOOPEND,PUTCHAR,
		GETCHAR,STORE,LOAD,RET,COMMENT);
	signal instr_dec : instr;

	type fsm is (FETCH, DECODE,INCPTRDATA_S, DECPTRDATA_S, PUTCHAR_S, PUTCHAR_S1, GETCHAR_S,LOAD_S,ZOMBIE,LOOP_START_1,LOOP_START_2,LOOP_START_3,LOOP_END_1,LOOP_END_2,LOOP_END_3,LOOP_END_4);
	signal currentstate,nextstate : fsm;

	signal ld : std_logic := '0';
	signal tmp_data : std_logic_vector(7 downto 0);

	signal data_rdata_inc : std_logic_vector(7 downto 0);
	signal data_rdata_dec : std_logic_vector(7 downto 0);
	signal data_rdara_bit : std_logic;
	signal sel : std_logic_vector(1 downto 0);

begin

--------------------------------------------------------------------------
--
--							PROGRAM COUTER
--
--------------------------------------------------------------------------

	PC: process (CLK,RESET,pc_inc,pc_dec)
		begin
			if (RESET = '1') then
				pc_out <= (others => '0');
			elsif (CLK'EVENT and CLK = '1') then
        if (pc_inc = '1') then
				  pc_out <= pc_out + 1;
				--co ak pretecie? vynulovat alebo drzat?
        elsif (pc_dec = '1') then
				  pc_out <= pc_out - 1;
				--co ak podtecie drzat 0? alebo
        end if; 
			end if;
		end process;
	CODE_ADDR <= pc_out;

--------------------------------------------------------------------------
--
--							NESTED WHILE REGISTER
--
--------------------------------------------------------------------------

	CNT: process (CLK,RESET,cnt_inc, cnt_dec)
		begin
			if (RESET = '1') then
				cnt_out <= (others => '0');
			elsif (CLK'EVENT and CLK = '1') then
        		if (cnt_inc = '1') then
				  cnt_out	<= cnt_out + 1;
				elsif (cnt_dec = '1') then
				  cnt_out <= cnt_out - 1;
			  	end if;
      		end if;				
		end process;

cnt_bit <= '1' when cnt_out = "00000000" else '0'; --ak je nulovy vektor, bit je 1 (true)

--------------------------------------------------------------------------
--
--							POINTER REGISTER
--
--------------------------------------------------------------------------

	PTR: process (CLK,RESET, ptr_inc, ptr_dec)
		begin
			if (RESET = '1') then 
				ptr_data <= (others => '0');
			elsif (CLK'EVENT and CLK = '1') then
        if (ptr_inc = '1') then
				  ptr_data <= ptr_data + 1;
				  if (ptr_data = "1111111111") then
					 ptr_data <= (others => '0');
				  end if;
        elsif (ptr_dec = '1') then
				  ptr_data <= ptr_data - 1;
				  if (ptr_data = "0000000000") then
				  	ptr_data <= (others => '1');
				  end if;
         end if; 
			end if;
		end process;
	DATA_ADDR <= ptr_data;

--------------------------------------------------------------------------
--
--							COMBINATIONAL DECODER
--
--------------------------------------------------------------------------

	DECODER_COMB: process(CODE_DATA)
		begin
			case CODE_DATA is
				when X"3E" => instr_dec <= INCPTR;
				when X"3C" => instr_dec <= DECPTR;
				when X"2B" => instr_dec <= INCPTRDATA;
				when X"2D" => instr_dec <= DECPTRDATA;
				when X"5B" => instr_dec <= LOOPSTART;
				when X"5D" => instr_dec <= LOOPEND;
				when X"2E" => instr_dec <= PUTCHAR;
				when X"2C" => instr_dec <= GETCHAR;
				when X"24" => instr_dec <= LOAD;
				when X"21" => instr_dec <= STORE;
				when X"00" => instr_dec <= RET;
				when others => instr_dec <= COMMENT;
			end case;
	end process;

--------------------------------------------------------------------------
--
--							TMP REGISTER
--
--------------------------------------------------------------------------

	TMP: process (CLK,RESET,ld)
		begin
			if (CLK'EVENT and CLK = '1' and ld = '1') then
				tmp_data <= DATA_RDATA;
			end if;
	end process;

--------------------------------------------------------------------------
--
--							MUX COMBINATIONAL
--
--------------------------------------------------------------------------

	data_rdara_bit <= '1' when DATA_RDATA = "00000000" else '0';
	data_rdata_inc <= DATA_RDATA + 1;
	data_rdata_dec <= DATA_RDATA - 1;
	with sel select DATA_WDATA <=
		IN_DATA when "00",
		tmp_data when "01",
		data_rdata_inc when "10",
		data_rdata_dec when others;
	OUT_DATA <= DATA_RDATA;

--------------------------------------------------------------------------
--
--							CONTROLLER (FSM)
--
--------------------------------------------------------------------------	

	CONTROLLER1: process(CLK,RESET,EN, nextstate)
		begin
			if (RESET = '1') then
				currentstate <= FETCH;
			elsif (CLK'EVENT and CLK = '1' and EN = '1') then
				currentstate <= nextstate;
			end if;
	end process;

	CONTROLLER2: process(currentstate,instr_dec,cnt_bit,data_rdara_bit,IN_VLD,OUT_BUSY, IN_DATA, DATA_RDATA)
		begin
		nextstate <= FETCH;
		sel <= "00";
		DATA_RDWR <= '1';
		CODE_EN <= '0';
		DATA_EN <= '0';
		OUT_WE <= '0';
		IN_REQ <= '0';
		pc_inc <= '0';
		pc_dec <= '0';
		ptr_inc <= '0';
		ptr_dec <= '0';
		ld <= '0';
		cnt_inc <= '0';
		cnt_dec <= '0';
		case currentstate is 
			when FETCH =>
				CODE_EN <= '1';
				nextstate <= DECODE;

			when DECODE => 
				case instr_dec is
					when INCPTR =>
						pc_inc <= '1';
						ptr_inc <= '1';
						nextstate <= FETCH;
					
					when DECPTR =>
						pc_inc <= '1';
						ptr_dec <= '1';
						nextstate <= FETCH;

					when INCPTRDATA =>
						DATA_EN <= '1';
						DATA_RDWR <= '1'; --read?
						nextstate <= INCPTRDATA_S;

					when DECPTRDATA =>
						DATA_EN <= '1';
						DATA_RDWR <= '1';
						nextstate <= DECPTRDATA_S;


 					when PUTCHAR =>
						if (OUT_BUSY = '1') then
							nextstate <= PUTCHAR_S;
						else 
							DATA_EN <= '1';
							DATA_RDWR <= '1';
							nextstate <= PUTCHAR_S1;
						end if;

					when GETCHAR =>
						IN_REQ <= '1';
						if (IN_VLD = '1') then
							DATA_EN <= '1';
							DATA_RDWR <= '0';
							sel <= "00";
							pc_inc <= '1';
							nextstate <= FETCH;
						else
							nextstate <= GETCHAR_S;
						end if;

					when LOAD =>
						DATA_EN <= '1';
						DATA_RDWR <= '1';
						nextstate <= LOAD_S;

					when STORE =>
						DATA_EN <= '1';
						DATA_RDWR <= '0';
						sel <= "01";
						pc_inc <= '1';
						nextstate <= FETCH;

					when LOOPSTART =>
						pc_inc <= '1';
						DATA_EN <= '1';
						DATA_RDWR <= '1';
						nextstate <= LOOP_START_1;

					when LOOPEND =>
						DATA_EN <= '1';
						DATA_RDWR <= '1';
						nextstate <= LOOP_END_1;

					when COMMENT =>	--ked je koment
						nextstate <= FETCH;
						pc_inc <= '1';

					when RET => 
						nextstate <= ZOMBIE;

					when others =>
						pc_inc <= '1';
						nextstate <= FETCH;

				end case;

			when INCPTRDATA_S =>
				DATA_EN <= '1';
				DATA_RDWR <= '0';
				sel <= "10";
				pc_inc <= '1';
				nextstate <= FETCH;

			when DECPTRDATA_S =>
				DATA_EN <= '1';
				DATA_RDWR <= '0';
				sel <= "11";
				pc_inc <= '1';
				nextstate <= FETCH;
      
      		when PUTCHAR_S =>
				if (OUT_BUSY = '1') then
					nextstate <= PUTCHAR_S;
				else
					DATA_EN <= '1';
					DATA_RDWR <= '1';
					
					nextstate <= PUTCHAR_S1;
				end if ;

			when PUTCHAR_S1 =>
        		OUT_WE <= '1';
				pc_inc <= '1';
				nextstate <= FETCH;

			when GETCHAR_S =>
				IN_REQ <= '1';
				if (IN_VLD = '1') then
					DATA_EN <= '1';
					DATA_RDWR <= '0';
					sel <= "00";
					pc_inc <= '1';
					nextstate <= FETCH;
				else
					nextstate <= GETCHAR_S;
				end if;

			when LOAD_S =>
				ld <= '1';
				pc_inc <= '1';
				nextstate <= FETCH;

			when LOOP_START_1 =>
				if (data_rdara_bit = '1') then
					cnt_inc <= '1';
					nextstate <= LOOP_START_2;
				else
					nextstate <= FETCH;
				end if;

			when LOOP_START_2 =>
				if (cnt_bit = '0') then
					CODE_EN <= '1';
					nextstate <= LOOP_START_3;
				else
					nextstate <= FETCH;
				end if;

			when LOOP_START_3 =>
				if (instr_dec = LOOPSTART) then
					cnt_inc <= '1';
				elsif (instr_dec = LOOPEND) then
					cnt_dec <= '1';					
				end if;
				nextstate <= LOOP_START_2;	
				pc_inc <= '1';

			when LOOP_END_1 =>
				if (data_rdara_bit = '1') then
					pc_inc <= '1';
					nextstate <= FETCH;
				else
					cnt_inc <= '1';
					pc_dec <= '1';
					nextstate <= LOOP_END_2;
				end if;

			when LOOP_END_2 =>
				if (cnt_bit = '0') then
					CODE_EN <= '1';
					nextstate <= LOOP_END_3;
				else
					nextstate <= FETCH;
				end if;

			when LOOP_END_3 =>
				if (instr_dec = LOOPEND) then
					cnt_inc <= '1';
					nextstate <= LOOP_END_4;
				elsif (instr_dec = LOOPSTART) then
					cnt_dec <= '1';
					nextstate <= LOOP_END_4;
				end if;
				nextstate <= LOOP_END_4;

			when LOOP_END_4 =>
				if (cnt_bit = '1') then
					pc_inc <= '1';
				else
					pc_dec <= '1';
				end if;
				nextstate <= LOOP_END_2;

			when ZOMBIE =>
				nextstate <= ZOMBIE;

			when others =>
				nextstate <= ZOMBIE;
				pc_inc <= '1';

		end case;		

	end process;
end behavioral;
 
