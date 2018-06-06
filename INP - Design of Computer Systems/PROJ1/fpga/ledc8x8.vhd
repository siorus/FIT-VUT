library IEEE;
use IEEE.std_logic_1164.all;
use IEEE.std_logic_arith.all;
use IEEE.std_logic_unsigned.all;

entity ledc8x8 is
port ( -- Sem doplnte popis rozhrani obvodu.
		SMCLK 	:	in STD_LOGIC;
		RESET	:	in STD_LOGIC;
		ROW 	:	out STD_LOGIC_VECTOR (7 downto 0);
		LED		:	out STD_LOGIC_VECTOR (7 downto 0)
	);
end ledc8x8;

architecture main of ledc8x8 is

   signal LED_SIG 		: STD_LOGIC_VECTOR (7 downto 0) := "11000001";
	signal ROW_SIG 		: STD_LOGIC_VECTOR (7 downto 0) := "10000000";
	signal HERTZ_CLK 	: STD_LOGIC := '0';
	signal CNT_HERTZ	: INTEGER range 0 to 7372800 := 0;
	signal SMCLK_256 	: STD_LOGIC_VECTOR (7 downto 0) := "00000000";
	signal CE			: STD_LOGIC := '0';

begin
smclk_256_gen: Process(SMCLK, RESET)
	Begin
		if (RESET = '1') then
			SMCLK_256 <= "00000000";
		elsif (SMCLK'EVENT and SMCLK = '1') then
			
			if (SMCLK_256(6 downto 0) = "1111111") then
					CE <= '1';
				else
					CE <= '0';
				end if;
			
			if (SMCLK_256 = "11111111") then
				
				SMCLK_256 <= "00000000";
			else 
				SMCLK_256 <= SMCLK_256 + 1;
			end if;
		end if;
	End Process;	
--------------------------------------------------------------	

half_hertz_gen: Process(SMCLK,RESET)
	Begin
		if (RESET = '1') then
			HERTZ_CLK <= '0';
			CNT_HERTZ <= 0;
		elsif (SMCLK'EVENT and SMCLK = '1') then
			if (CNT_HERTZ = 3686399) then
				HERTZ_CLK <= not HERTZ_CLK;
				CNT_HERTZ <= 0;
			else
				CNT_HERTZ <= CNT_HERTZ + 1;
			end if;
		end if;
	End Process;

---------------------------------------------------------------	
	
row_generator: Process(SMCLK,RESET)
	Begin
		if (RESET = '1') then
			ROW_SIG <= "10000000";
		elsif (SMCLK'EVENT and SMCLK = '1') then
			if (CE = '1') then
				if (ROW_SIG = "00000001") then
					ROW_SIG <= "10000000";
				else
					ROW_SIG <= '0' & ROW_SIG(7 downto 1);				
				end if;
			end if;
		end if;			
	End Process;
	
----------------------------------------------------------------	

	display: Process(ROW_SIG, HERTZ_CLK)
		Begin
			if (HERTZ_CLK = '0') then
				case (ROW_SIG) is
					when "00000001" => LED_SIG <= "11000001";
					when "00000010" => LED_SIG <= "11011111";
					when "00000100" => LED_SIG <= "11011111";
					when "00001000" => LED_SIG <= "11011111";
					when "00010000" => LED_SIG <= "11011111";
					when "00100000" => LED_SIG <= "11011111";
					when "01000000" => LED_SIG <= "11011101";
					when "10000000" => LED_SIG <= "11100011";
					when others		 => LED_SIG <= "11111111";
				end case;
			else
				case (ROW_SIG) is
					when "00000001" => LED_SIG <= "11011101";
					when "00000010" => LED_SIG <= "11101101";
					when "00000100" => LED_SIG <= "11110101";
					when "00001000" => LED_SIG <= "11111001";
					when "00010000" => LED_SIG <= "11111001";
					when "00100000" => LED_SIG <= "11110101";
					when "01000000" => LED_SIG <= "11101101";
					when "10000000" => LED_SIG <= "11011101";
					when others		 => LED_SIG <= "11111111";
				end case;
			end if;

	End Process;
	
		ROW <= ROW_SIG;
		LED <= LED_SIG;
end main;
