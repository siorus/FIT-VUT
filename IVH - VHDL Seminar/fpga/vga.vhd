
library ieee;
use ieee.std_logic_1164.all;
use ieee.std_logic_arith.all;
use ieee.std_logic_unsigned.all;
use work.vga_controller_cfg.all;
use work.math_pack2.ALL;

architecture main of tlv_pc_ifc is

   signal vga_mode  : std_logic_vector(60 downto 0); -- default 640x480x60

   signal irgb : std_logic_vector(8 downto 0);
   alias red is irgb(8 downto 6);
   alias green is irgb(5 downto 3);
   alias blue is irgb(2 downto 0);
	signal clr_actv : std_logic_vector(8 downto 0):= "111111000";
	signal clr_not_actv : std_logic_vector(8 downto 0):= "001001001";
	signal CLOCK:std_logic := '0';
   signal RST:std_logic := '0';
	signal kbrd_data_out : std_logic_vector(15 downto 0);
   signal kbrd_data_vld : std_logic;	
	signal row : std_logic_vector(11 downto 0);
   signal col : std_logic_vector(11 downto 0);
   signal INVERT_REQ   :   STD_LOGIC_VECTOR (99 downto 0);
   signal SELECT_REQ   :   STD_LOGIC_VECTOR (99 downto 0);
   signal INIT_ACT     :   STD_LOGIC_VECTOR (24 downto 0);
   signal ACTIVE_CELL  :   STD_LOGIC_VECTOR (24 downto 0);      
   signal INIT_SEL     :   STD_LOGIC_VECTOR (24 downto 0);
   signal SELECTED_CELL:   STD_LOGIC_VECTOR (24 downto 0);
	signal NUMBER1			: 	STD_LOGIC_VECTOR (3 downto 0) := (others=>'0');
	signal NUMBER2			: 	STD_LOGIC_VECTOR (3 downto 0) := (others=>'0');
	signal NUMBER3			:	STD_LOGIC_VECTOR (3 downto 0) := (others=>'0');
	signal KEYS				:	STD_LOGIC_VECTOR (4 downto 0);
	constant IDX_TOP    : NATURAL := 0; -- index sousedni bunky nachazejici se nahore v signalech *_REQ_IN a *_REQ_OUT, index klavesy posun nahoru v KEYS
	constant IDX_LEFT   : NATURAL := 1; -- ... totez        ...                vlevo
	constant IDX_RIGHT  : NATURAL := 2; -- ... totez        ...                vpravo
	constant IDX_BOTTOM : NATURAL := 3;
	constant IDX_ENTER  : NATURAL := 4;
	
begin

   -- Nastaveni grafickeho rezimu (640x480, 60 Hz refresh)
	
   setmode(r640x480x60, vga_mode);

   vga: entity work.vga_controller(arch_vga_controller) 
      generic map (REQ_DELAY => 1)
      port map (
         CLK    => CLK, 
         RST    => RESET,
         ENABLE => '1',
         MODE   => vga_mode,

         DATA_RED    => red,
         DATA_GREEN  => green,
         DATA_BLUE   => blue,
         ADDR_COLUMN => col,
         ADDR_ROW    => row,

         VGA_RED   => RED_V,
         VGA_BLUE  => BLUE_V,
         VGA_GREEN => GREEN_V,
         VGA_HSYNC => HSYNC_V,
         VGA_VSYNC => VSYNC_V
         
      );
	bcd: entity work.bcd
		port map (
						CLK    => CLOCK, 
						RESET    => RST,
						NUMBER1 => NUMBER1,
						NUMBER2 => NUMBER2,
						NUMBER3 => NUMBER3
		);
	
	kbrd_ctrl: entity work.keyboard_controller(arch_keyboard)
      port map (
         CLK => SMCLK,
         RST => RESET,

         DATA_OUT => kbrd_data_out(15 downto 0),
         DATA_VLD => kbrd_data_vld,
         
         KB_KIN   => KIN,
         KB_KOUT  => KOUT
      );
		
		stlpec: for x in 0 to 4 generate
         riadok: for y in 0 to 4 generate

           matrix:  entity work.cell
                  generic map (MASK => getmask(x,y,5,5))

                    port map (  KEYS => KEYS,
											RESET => RST,
											CLK => CLK,
                                 INVERT_REQ_IN(IDX_TOP)  => INVERT_REQ((((x mod 5)+((y-1) mod 5)*5)*4 + (IDX_BOTTOM mod 4))), 
                                 INVERT_REQ_IN(IDX_LEFT)  => INVERT_REQ(((((x-1) mod 5)+(y mod 5)*5)*4 + (IDX_RIGHT mod 4))), 
                                 INVERT_REQ_IN(IDX_RIGHT)  => INVERT_REQ(((((x+1) mod 5)+(y mod 5)*5)*4 + (IDX_LEFT mod 4))), 
                                 INVERT_REQ_IN(IDX_BOTTOM)  => INVERT_REQ((((x mod 5)+((y+1) mod 5)*5)*4 + (IDX_TOP mod 4))), 
                                 INVERT_REQ_OUT => INVERT_REQ ((x+y*5)*4+3 downto (x+y*5)*4),
                                 SELECT_REQ_IN(IDX_TOP)  => SELECT_REQ((((x mod 5)+((y-1) mod 5)*5)*4 + (IDX_BOTTOM mod 4))),
                                 SELECT_REQ_IN(IDX_LEFT)  => SELECT_REQ(((((x-1) mod 5)+(y mod 5)*5)*4 + (IDX_RIGHT mod 4))),
                                 SELECT_REQ_IN(IDX_RIGHT)  => SELECT_REQ(((((x+1) mod 5)+(y mod 5)*5)*4 + (IDX_LEFT mod 4))),
                                 SELECT_REQ_IN(IDX_BOTTOM)  => SELECT_REQ((((x mod 5)+((y+1) mod 5)*5)*4 + (IDX_TOP mod 4))),
											SELECT_REQ_OUT => SELECT_REQ((x+y*5)*4+3 downto (x+y*5)*4),
                                 SELECTED => SELECTED_CELL(x+y*5),
                                 ACTIVE => ACTIVE_CELL(x+y*5),
											INIT_ACTIVE => INIT_ACT(x+y*5),
											INIT_SELECTED => INIT_SEL(x+y*5)
                              );
         end generate riadok;
      end generate stlpec;
	process (clk)
		begin
		if (CLK'EVENT and CLK = '1') then
			if (col >= 160 and col <= 224 and row >= 80 and row <= 144) then
				if (ACTIVE_CELL(0) = '1') then 
					irgb <= clr_actv; 
				else irgb <= clr_not_actv; 
				end if;
				if (col >= 176 and col <= 208 and row >= 96 and row <= 128) then
					if (SELECTED_CELL(0) = '1') then
						irgb <= "111111111";
					end if;
				end if;
			elsif (col >= 225 and col <= 289 and row >= 80 and row <= 144) then
				if (ACTIVE_CELL(1) = '1') then 
					irgb <= clr_actv; 
				else irgb <= clr_not_actv; 
				end if;
				if (col >= 241 and col <= 273 and row >= 96 and row <= 128) then
					if (SELECTED_CELL(1) = '1') then
						irgb <= "111111111";
					end if;
				end if;
			elsif (col >= 290 and col <= 354 and row >= 80 and row <= 144) then
				if (ACTIVE_CELL(2) = '1') then 
					irgb <= clr_actv; 
				else irgb <= clr_not_actv; 
				end if;
				if (col >= 306 and col <= 338 and row >= 96 and row <= 128) then
					if (SELECTED_CELL(2) = '1') then
						irgb <= "111111111";
					end if;
				end if;
			elsif (col >= 355 and col <= 419 and row >= 80 and row <= 144) then
				if (ACTIVE_CELL(3) = '1') then 
					irgb <= clr_actv; 
				else irgb <= clr_not_actv; 
				end if;
				if (col >= 371 and col <= 403 and row >= 96 and row <= 128) then
					if (SELECTED_CELL(3) = '1') then
						irgb <= "111111111";
					end if;
				end if;
			elsif (col >= 420 and col <= 484 and row >= 80 and row <= 144) then
				if (ACTIVE_CELL(4) = '1') then 
					irgb <= clr_actv; 
				else irgb <= clr_not_actv; 
				end if;
				if (col >= 436 and col <= 468 and row >= 96 and row <= 128) then
					if (SELECTED_CELL(4) = '1') then
						irgb <= "111111111";
					end if;
				end if;
				
			elsif (col >= 160 and col <= 224 and row >= 145 and row <= 209) then
				if (ACTIVE_CELL(5) = '1') then 
					irgb <= clr_actv; 
				else irgb <= clr_not_actv; 
				end if;
				if (col >= 176 and col <= 208 and row >= 161 and row <= 193) then
					if (SELECTED_CELL(5) = '1') then
						irgb <= "111111111";
					end if;
				end if;
			elsif (col >= 225 and col <= 289 and row >= 145 and row <= 209) then
				if (ACTIVE_CELL(6) = '1') then 
					irgb <= clr_actv; 
				else irgb <= clr_not_actv; 
				end if;
				if (col >= 241 and col <= 273 and row >= 161 and row <= 193) then
					if (SELECTED_CELL(6) = '1') then
						irgb <= "111111111";
					end if;
				end if;
			elsif (col >= 290 and col <= 354 and row >= 145 and row <= 209) then
				if (ACTIVE_CELL(7) = '1') then 
					irgb <= clr_actv; 
				else irgb <= clr_not_actv; 
				end if;
				if (col >= 306 and col <= 338 and row >= 161 and row <= 193) then
					if (SELECTED_CELL(7) = '1') then
						irgb <= "111111111";
					end if;
				end if;
			elsif (col >= 355 and col <= 419 and row >= 145 and row <= 209) then
				if (ACTIVE_CELL(8) = '1') then 
					irgb <= clr_actv; 
				else irgb <= clr_not_actv; 
				end if;
				if (col >= 371 and col <= 403 and row >= 161 and row <= 193) then
					if (SELECTED_CELL(8) = '1') then
						irgb <= "111111111";
					end if;
				end if;
			elsif (col >= 420 and col <= 484 and row >= 145 and row <= 209) then
				if (ACTIVE_CELL(9) = '1') then 
					irgb <= clr_actv; 
				else irgb <= clr_not_actv; 
				end if;
				if (col >= 436 and col <= 468 and row >= 161 and row <= 193) then
					if (SELECTED_CELL(9) = '1') then
						irgb <= "111111111";
					end if;
				end if;
				
			elsif (col >= 160 and col <= 224 and row >= 210 and row <= 274) then
				if (ACTIVE_CELL(10) = '1') then 
					irgb <= clr_actv; 
				else irgb <= clr_not_actv; 
				end if;
				if (col >= 176 and col <= 208 and row >= 226 and row <= 258) then
					if (SELECTED_CELL(10) = '1') then
						irgb <= "111111111";
					end if;
				end if;
			elsif (col >= 225 and col <= 289 and row >= 210 and row <= 274) then
				if (ACTIVE_CELL(11) = '1') then 
					irgb <= clr_actv; 
				else irgb <= clr_not_actv; 
				end if;
				if (col >= 241 and col <= 273 and row >= 226 and row <= 258) then
					if (SELECTED_CELL(11) = '1') then
						irgb <= "111111111";
					end if;
				end if;
			elsif (col >= 290 and col <= 354 and row >= 210 and row <= 274) then
				if (ACTIVE_CELL(12) = '1') then 
					irgb <= clr_actv; 
				else irgb <= clr_not_actv; 
				end if;
				if (col >= 306 and col <= 338 and row >= 226 and row <= 258) then
					if (SELECTED_CELL(12) = '1') then
						irgb <= "111111111";
					end if;
				end if;
			elsif (col >= 355 and col <= 419 and row >= 210 and row <= 274) then
				if (ACTIVE_CELL(13) = '1') then 
					irgb <= clr_actv; 
				else irgb <= clr_not_actv; 
				end if;
				if (col >= 371 and col <= 403  and row >= 226 and row <= 258) then
					if (SELECTED_CELL(13) = '1') then
						irgb <= "111111111";
					end if;
				end if;
			elsif (col >= 420 and col <= 484 and row >= 210 and row <= 274) then
				if (ACTIVE_CELL(14) = '1') then 
					irgb <= clr_actv; 
				else irgb <= clr_not_actv; 
				end if;
				if (col >= 436 and col <= 468 and row >= 226 and row <= 258) then
					if (SELECTED_CELL(14) = '1') then
						irgb <= "111111111";
					end if;
				end if;
				
			elsif (col >= 160 and col <= 224 and row >= 275 and row <= 339) then
				if (ACTIVE_CELL(15) = '1') then 
					irgb <= clr_actv; 
				else irgb <= clr_not_actv; 
				end if;
				if (col >= 176 and col <= 208 and row >= 291 and row <= 323) then
					if (SELECTED_CELL(15) = '1') then
						irgb <= "111111111";
					end if;
				end if;
			elsif (col >= 225 and col <= 289 and row >= 275 and row <= 339) then
				if (ACTIVE_CELL(16) = '1') then 
					irgb <= clr_actv; 
				else irgb <= clr_not_actv; 
				end if;
				if (col >= 241 and col <= 273 and row >= 291 and row <= 323) then
					if (SELECTED_CELL(16) = '1') then
						irgb <= "111111111";
					end if;
				end if;
			elsif (col >= 290 and col <= 354 and row >= 275 and row <= 339) then
				if (ACTIVE_CELL(17) = '1') then 
					irgb <= clr_actv; 
				else irgb <= clr_not_actv; 
				end if;
				if (col >= 306 and col <= 338 and row >= 291 and row <= 323) then
					if (SELECTED_CELL(17) = '1') then
						irgb <= "111111111";
					end if;
				end if;
			elsif (col >= 355 and col <= 419 and row >= 275 and row <= 339) then
				if (ACTIVE_CELL(18) = '1') then 
					irgb <= clr_actv; 
				else irgb <= clr_not_actv; 
				end if;
				if (col >= 371 and col <= 403 and row >= 291 and row <= 323) then
					if (SELECTED_CELL(18) = '1') then
						irgb <= "111111111";
					end if;
				end if;
			elsif (col >= 420 and col <= 484 and row >= 275 and row <= 339) then
				if (ACTIVE_CELL(19) = '1') then 
					irgb <= clr_actv; 
				else irgb <= clr_not_actv; 
				end if;
				if (col >= 436 and col <= 468 and row >= 291 and row <= 323) then
					if (SELECTED_CELL(19) = '1') then
						irgb <= "111111111";
					end if;
				end if;
			
			elsif (col >= 160 and col <= 224 and row >= 340 and row <= 404) then
				if (ACTIVE_CELL(20) = '1') then 
					irgb <= clr_actv; 
				else irgb <= clr_not_actv; 
				end if;
				if (col >= 176 and col <= 208 and row >= 356 and row <= 388) then
					if (SELECTED_CELL(20) = '1') then
						irgb <= "111111111";
					end if;
				end if;
			elsif (col >= 225 and col <= 289 and row >= 340 and row <= 404) then
				if (ACTIVE_CELL(21) = '1') then 
					irgb <= clr_actv; 
				else irgb <= clr_not_actv; 
				end if;
				if (col >= 241 and col <= 273 and row >= 356 and row <= 388) then
					if (SELECTED_CELL(21) = '1') then
						irgb <= "111111111";
					end if;
				end if;
			elsif (col >= 290 and col <= 354 and row >= 340 and row <= 404) then
				if (ACTIVE_CELL(22) = '1') then 
					irgb <= clr_actv; 
				else irgb <= clr_not_actv; 
				end if;
				if (col >= 306 and col <= 338 and row >= 356 and row <= 388) then
					if (SELECTED_CELL(22) = '1') then
						irgb <= "111111111";
					end if;
				end if;
			elsif (col >= 355 and col <= 419 and row >= 340 and row <= 404) then
				if (ACTIVE_CELL(23) = '1') then 
					irgb <= clr_actv; 
				else irgb <= clr_not_actv; 
				end if;
				if (col >= 371 and col <= 403 and row >= 356 and row <= 388) then
					if (SELECTED_CELL(23) = '1') then
						irgb <= "111111111";
					end if;
				end if;
			elsif (col >= 420 and col <= 484 and row >= 340 and row <= 404) then
				if (ACTIVE_CELL(24) = '1') then 
					irgb <= clr_actv; 
				else irgb <= clr_not_actv; 
				end if;
				if (col >= 436 and col <= 468 and row >= 356 and row <= 388) then
					if (SELECTED_CELL(24) = '1') then
						irgb <= "111111111";
					end if;
				end if;
			else irgb <= "000000000";
			end if;		
		end if;
		end process;
		
		
	cursor: process
		variable in_access : std_logic := '0';
   begin
      if CLK'event and CLK='1' then
         KEYS <= "00000";
         RST <= '0';                  
         if in_access='0' then
            if kbrd_data_vld='1' then 
               in_access:='1';
               if kbrd_data_out(9)='1' then  -- key 6
                  KEYS(IDX_RIGHT) <= '1';
               elsif kbrd_data_out(1)='1' then  -- key 4
                  KEYS(IDX_LEFT) <= '1';
               elsif kbrd_data_out(4)='1' then  -- key 2
                  KEYS(IDX_TOP) <= '1';
               elsif kbrd_data_out(6)='1' then  -- key 8
                  KEYS(IDX_BOTTOM) <= '1';
               elsif kbrd_data_out(5)='1' then     -- key 5
                  KEYS(IDX_ENTER) <= '1';
					elsif (kbrd_data_out(12) = '1') then
						INIT_ACT <= "1010010101111001110101011";
						INIT_SEL <= "0000000000001000000000000";
					RST <= '1';
					elsif (kbrd_data_out(13) = '1') then
						INIT_ACT <= "1111111111110111111111111";
						INIT_SEL <= "0000000000001000000000000";
					RST <= '1';
					elsif (kbrd_data_out(14) = '1') then
						INIT_ACT <= "0111100011010101111110100";
						INIT_SEL <= "0000000000001000000000000";
					RST <= '1';
					elsif (kbrd_data_out(15) = '1') then
						INIT_ACT <= "1111100000111110000011111";
						INIT_SEL <= "0000000000001000000000000";
					RST <= '1';
               end if;
            end if;
         else
            if kbrd_data_vld='0' then 
               in_access:='0';
            end if;
         end if;
      end if;
      
   end process;

end main;