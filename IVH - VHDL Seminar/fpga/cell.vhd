----------------------------------------------------------------------------------
-- Engineer: 
----------------------------------------------------------------------------------
library IEEE;
use IEEE.STD_LOGIC_1164.ALL;
use work.math_pack2.ALL; -- vysledek z prvniho ukolu


entity cell is
   GENERIC (
      MASK              : mask_t := (others => '1') -- mask_t definovano v baliku math_pack
   );
   Port ( 
      INVERT_REQ_IN     : in   STD_LOGIC_VECTOR (3 downto 0);
      INVERT_REQ_OUT    : out  STD_LOGIC_VECTOR (3 downto 0);
      
      KEYS              : in   STD_LOGIC_VECTOR (4 downto 0);
      
      SELECT_REQ_IN     : in   STD_LOGIC_VECTOR (3 downto 0);
      SELECT_REQ_OUT    : out  STD_LOGIC_VECTOR (3 downto 0);
      
      INIT_ACTIVE       : in   STD_LOGIC;
      ACTIVE            : out  STD_LOGIC;
      
      INIT_SELECTED     : in   STD_LOGIC;
      SELECTED          : out  STD_LOGIC;

      CLK               : in   STD_LOGIC;
      RESET             : in   STD_LOGIC
   );
end cell;

architecture Behavioral of cell is
  constant IDX_TOP    : NATURAL := 0; -- index sousedni bunky nachazejici se nahore v signalech *_REQ_IN a *_REQ_OUT, index klavesy posun nahoru v KEYS
  constant IDX_LEFT   : NATURAL := 1; -- ... totez        ...                vlevo
  constant IDX_RIGHT  : NATURAL := 2; -- ... totez        ...                vpravo
  constant IDX_BOTTOM : NATURAL := 3; -- ... totez        ...                dole
  
  constant IDX_ENTER  : NATURAL := 4; -- index klavesy v KEYS, zpusobujici inverzi bunky (enter, klavesa 5)
	signal selected_req_out : STD_LOGIC_VECTOR (3 downto 0):=(others => '0');
	signal inverted_req_out : STD_LOGIC_VECTOR (3 downto 0):=(others => '0');
	signal selected_sig : STD_LOGIC;
	signal active_sig: STD_LOGIC;
	
begin
	SELECT_REQ_OUT <= selected_req_out;
	INVERT_REQ_OUT <= inverted_req_out;
	SELECTED <= selected_sig;
	ACTIVE <= active_sig;
	process (CLK,RESET)
		begin
			if (RESET = '1') then
				active_sig <= INIT_ACTIVE;
				selected_sig <= INIT_SELECTED;			
			elsif (CLK'EVENT and CLK = '1') then
				if inverted_req_out /= "0000" then
					inverted_req_out <= "0000";
				end if;
				if selected_req_out /= "0000" then
					selected_req_out <= "0000";
				end if;
				if selected_sig = '1' then 
					if KEYS(IDX_TOP) = '1' and mask.top = '1' then
						selected_req_out(IDX_TOP) <= '1';
						selected_sig <= '0';
					end if;
					if KEYS(IDX_LEFT) = '1' and mask.left = '1' then
						selected_req_out(IDX_LEFT) <= '1';
						selected_sig <= '0';
					end if;
					if KEYS(IDX_RIGHT) = '1' and mask.right = '1' then
						selected_req_out(IDX_RIGHT) <= '1';
						selected_sig <= '0';
					end if;
					if KEYS(IDX_BOTTOM) = '1' and mask.bottom = '1' then
						selected_req_out(IDX_BOTTOM) <= '1';
						selected_sig <= '0';
					end if;
					if KEYS(IDX_ENTER) = '1' then
						active_sig <= not active_sig;
						if mask.top = '1' then
							inverted_req_out(IDX_TOP) <= '1';
						end if;
						if mask.right = '1' then
							inverted_req_out(IDX_RIGHT) <= '1';
						end if;
						if mask.left = '1' then
							inverted_req_out(IDX_LEFT) <= '1';
						end if;
						if mask.bottom = '1' then
							inverted_req_out(IDX_BOTTOM) <= '1';
						end if;
					end if;

				end if;
				
				if selected_sig = '0' then
					if SELECT_REQ_IN(IDX_TOP) = '1' and mask.top = '1' then
						selected_sig <= '1';
					end if;
					if SELECT_REQ_IN(IDX_LEFT) = '1' and mask.left = '1' then
						selected_sig <= '1';
					end if;
					if SELECT_REQ_IN(IDX_RIGHT) = '1' and mask.right = '1' then
						selected_sig <= '1';
					end if;
					if SELECT_REQ_IN(IDX_BOTTOM) = '1' and mask.bottom = '1' then
						selected_sig <= '1';
					end if;
					if INVERT_REQ_IN(IDX_TOP) = '1' and mask.top = '1' then
						active_sig <= not active_sig;
					end if;
					if INVERT_REQ_IN(IDX_LEFT) = '1' and mask.left = '1' then
						active_sig <= not active_sig;
					end if;
					if INVERT_REQ_IN(IDX_RIGHT) = '1' and mask.right = '1' then
						active_sig <= not active_sig;
					end if;
					if INVERT_REQ_IN(IDX_BOTTOM) = '1' and mask.bottom = '1' then
						active_sig <= not active_sig;
					end if;	
								
					
				end if;		
			end if;
			
				
		end process;
		
-- Pozadavky na funkci (sekvencni chovani vazane na vzestupnou hranu CLK)
--   pri resetu se nastavi ACTIVE a SELECTED na vychozi hodnotu danou signaly INIT_ACTIVE a INIT_SELECTED
--   pokud je bunka vybrana a prijde signal z klavesnice, tak se bud presune vyber pomoci SELECT_REQ na dalsi bunky nebo se invertuje stav bunky a jejiho okoli pomoci INVERT_REQ (klavesa ENTER)
--   pokud bunka neni vybrana a prijde signal INVERT_REQ, invertuje svuj stav
--   pozadavky do okolnich bunek se posilaji a z okolnich bunek prijimaji, jen pokud je maska na prislusne pozici v '1'

end Behavioral;

