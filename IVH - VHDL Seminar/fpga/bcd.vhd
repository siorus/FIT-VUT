library IEEE;
use IEEE.STD_LOGIC_1164.ALL;
use ieee.std_logic_unsigned.all;

entity bcd is

port(
	RESET, CLK: in STD_LOGIC;
	NUMBER1, NUMBER2, NUMBER3: BUFFER STD_LOGIC_VECTOR (3 downto 0) := (others=>'0')
);

end bcd;

architecture Behavioral of bcd is

begin
	process (CLK, RESET)
	begin
		if (RESET = '1') then
					NUMBER1 <= (others=>'0');
					NUMBER2 <= (others=>'0');
					NUMBER3 <= (others=>'0');
		elsif (CLK'EVENT and CLK = '1') then
					NUMBER1 <= NUMBER1 + 1;
					if (NUMBER1 = "1001") then
						NUMBER1 <= "0000";
						NUMBER2 <= NUMBER2 + 1;
						if (NUMBER2 = "1001") then
								NUMBER2 <= "0000";
								NUMBER3 <= NUMBER3 + 1;						
									if (NUMBER3 = "1001") then
										NUMBER3 <= "0000";							
									end if;
						end if;
					end if;
				end if;
	end process;
end Behavioral;