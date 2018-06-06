#!/usr/bin/env python3
"""
Projekt c.2 z ISJ
Nazov:	Polynomy
Autor:	Juraj Korcek, xkorce01
Datum:	14.4.2016
Popis: 	Program inicializuje polynom ako objekt, umoznuje scitavat, umocnovat,derivovat
		polynomy a vypocitat koren polynomu		
"""
class Polynomial:
    
    def __init__(self, *args, **kwargs):
        zoznam = []
        if len(args) == 1 and type(args[0]) is list:                # ak je jeden argument je to pole
                        zoznam = args[0]
                        self.polynom = zoznam
                        
        elif len(args) > 1 and type(args[0]) is int:
                        for i in range(len(args)):
                            zoznam.append(args[i])                  # ak je ich viac su ako argumenty dane do pola
                        self.polynom = zoznam
                           
        else:                                                       # inak je to slovnik
            maxmoc = int((max(sorted(kwargs.keys())))[1:])          # vypocet maximalneho clena
            pomocny = []
                                               
            for y in range(maxmoc+1):                               # iterovanie cez cleny pomocneho zoznamu aj s nulovymi koeficientmi
                yy=str(y)
                pomocny.append('x'+yy)								# vytvorim si pole aj s nulovymi koeficientami
                        
            for i in sorted(pomocny):
                try:
                    zoznam.append(kwargs[i])
                except KeyError:
                                zoznam.append(0)
            
            self.polynom= zoznam         
    def derivative(self):
        polynom2=[]
        for i in range(len(self.polynom)):
            polynom2.append(self.polynom[i]*i)						# vypocet derivacie
       
        return Polynomial(polynom2[1:])								# odsekne posledny clen, teda nulu
    
    def at_value(self, *args):										# za x dosadi zadane cislo
        if len(args) == 1:											# ak je zadana iba jedno x
                            x = args[0]								# nacitanie x
                            vysledok = 0
        
                            for i in range(len(self.polynom)):
                                        vysledok = vysledok + (self.polynom[i]*x**i)
                                    
        if len(args) == 2:											# ak su zadane dve x, vysledok je rozdiel vysledkov dvoch polynomov 
                            x1 = args[0]
                            x2 = args[1]
                            vysledok1 = 0
                            vysledok2 = 0
                            
                            for i in range(len(self.polynom)):
                                        vysledok1 = vysledok1 + (self.polynom[i]*x1**i)
                                        
                            for i in range(len(self.polynom)):
                                        vysledok2 = vysledok2 + (self.polynom[i]*x2**i)
                            
                            vysledok = vysledok2 - vysledok1                  
                
        return vysledok
    
    def __add__(self,other):																	# scitanie dvoch polynomov
        if len(self.polynom)>len(other.polynom):
                                dlzka = len(self.polynom)										# prvy polynom je dlzhsi
        else:
            dlzka = len(other.polynom)															# druhy polynom je dlhsi
        suma = []
        for i in range(dlzka):																	# scitanie, pocet iteracii podla dlhsieho
                            if len(self.polynom) > len(other.polynom):
                                        try:                         
                                            suma.append(self.polynom[i]+other.polynom[i])		# vyskusa pridat na koniec
                                        except IndexError:										# ak je uz na konci prida nulu a aj tak pripocita zvysne indexy
                                                other.polynom.append(0)
                                                suma.append(self.polynom[i]+other.polynom[i])
                            else:
                                try:                         
                                    suma.append(self.polynom[i]+other.polynom[i])
                                except IndexError:
                                    self.polynom.append(0)
                                    suma.append(self.polynom[i]+other.polynom[i])
        return Polynomial(suma)
 
    def __mul__ (self,other):
            polynom2 = [0]																		# nemoze byt prazdne, inak by neslo pridavat do neho
            for i in range (len(self.polynom)):
                for j in range (len(other.polynom)):
                    polynom2.insert(i+j,polynom2[i+j] + (self.polynom[i] * other.polynom[j]))	# nasobenie polynomu 
                    try: 
                        """
                        scita sa [8,9,0] + self.polynom[1]=2 tak vypise [8,11,9,0]
                        vyskusam teda ci za pridanym tj polynom[1] = 11 je este nieco
                        pred 0 a ak je tak to vymazem, lebo uz to je scitanie
                        a automaticky sa to stare na povodnom indexe posunulo
                        """
                        polynom2[i+j+2]
                        del polynom2[i+j+1] 													
                    except IndexError:
                                    None
            return Polynomial(polynom2[:-1])													# na poslednom indexe je ta 0 co bola pridana na zaciatku
        
    def __pow__ (self,mocnina):
        vysledok = self                                                                         # self je uz objekt 
        																		                      
        if type(mocnina) is int:
            for i in range(mocnina-1):                                                          # podla toho aka je mocnina zopakuje cyklus
                vysledok = vysledok * self
        else:
            return print("Mocnina v nasledujucom polynome je mimo rozsah")
                      
        polynom2 = []
        polynom2 = vysledok.polynom
                  
        return vysledok
    
    def __str__ (self):
        polynom2=""
        for i in reversed(range(len(self.polynom))):                                            # v opacnom poradi zacne iterovat
            if self.polynom[i] != 0 :                                                           # ak je koeficient 0 nic sa nevypise
                if i >= 2:
                    ii = "x^"+str(i)                                                            # exponent sa vypise ak je >=2
                elif i == 1:
                    ii = "x"                                                                    # ak je 1 vypise iba x
                elif i == 0:
                    ii = ""                                                                     # ak je to absolutny clen, nevypise x
                if (self.polynom[i] > 0) and (i != len(self.polynom)-1) and (polynom2 != ""):   
                    polynom2 = polynom2 + "+ "
                elif self.polynom[i] < 0:
                    polynom2 = polynom2 + "- "
                if ((self.polynom[i] == 1) or (self.polynom[i] == -1)) and (i != 0):
                    polynom2 = polynom2 + ii                    
                else:
                    polynom2 = polynom2 + str(abs(self.polynom[i])) + ii
                polynom2 = polynom2 + " "                                                       # pridam medzeru zakazdy vypis tj. - 3x^2 (medzera)

        polynom2 = polynom2.strip()                                                             # na konci odstrihne medzeru                            
        
        return polynom2            


if __name__ == "__main__":
    print ("test: Polynomial(2,1,6,0,9) ==", Polynomial(2,1,6,0,9))
    pol1 = Polynomial([1,-3,0,2])
    pol2 = Polynomial(1,-3,0,2)
    pol3 = Polynomial(x0=1,x3=2,x1=-3)
    print ("test: Polynomial([1,-3,0,2]) ==", pol1)
    print ("test: Polynomial(1,-3,0,2) ==", pol2)
    print ("test: Polynomial(x0=1,x3=2,x1=-3) ==", pol3)
    print ("test: Polynomial(1,-3,0,2) + Polynomial(0, 2, 1) ==", Polynomial(1,-3,0,2) + Polynomial(0, 2, 1))
    print ("test: Polynomial(-1, 1) ** 2 ==", Polynomial(-1, 1) ** 2)
    print ("test: pol1.derivative() ==", pol1.derivative())
    print ("test: pol1.at_value(2) ==", pol1.at_value(2))
    print ("test: pol1.at_value(2,3) ==", pol1.at_value(2,3))
    print ("test: Polynomial(-1, 1) ** 2.5 ==", Polynomial(-1, 1) ** 2.5)
    print ("test: Polynomial(-1, 1) ** 3 ==", Polynomial(-1, 1) ** 3)