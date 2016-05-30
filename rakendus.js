/* eslint-env browser */
'use strict';

//otsime üles selle id-ga nupu ja seame listeneri, mis toimub juhul kui käivitub klikk ja siis käivitab sellise funktsiooni
document.querySelector("#kuva-lisa-vorm").addEventListener(
    "click",
    function(event){
        document.querySelector("#lisa-vorm-vaade").style.display='block'; //otsitakse üles element vorm ja kuvatakse see (vaikimisi on none, mis on html lehel css-is määratud)
		document.querySelector("#kuva-lisa-vorm").style.display='none';
	}
);

document.querySelector("#peida-lisa-vorm").addEventListener(
    "click",
    function(event){
        document.querySelector("#lisa-vorm-vaade").style.display='none';
		document.querySelector("#kuva-lisa-vorm").style.display='block';
	}
);

document.querySelector("#lisa-vorm").addEventListener(
    "submit", //submit suunab lehelt minema, aga et seda ei juhtuks teeme eventi
    function(event){
        
        var nimetus = document.querySelector("#nimetus").value; //salvestame lahtri väärtuse muutujasse (string väärtus)
		var aeg = document.querySelector("#aeg").value;
        var kohad = Number(document.querySelector("#kohad").value); //number on ka string väärtus, et saaks aru, et number paneme Number()
		
        if (!nimetus || !aeg || kohad <= 0) {
            alert("Sisesta kõik andmed!");
            event.preventDefault(); //ära navigeeri lehelt minema
            return;
        }
	}
);

document.querySelector("#lisa-broneering").addEventListener(
    "submit", //submit suunab lehelt minema, aga et seda ei juhtuks teeme eventi
    function(event){
        
        var piletid = Number(document.querySelector("#piletid").value); //number on ka string väärtus, et saaks aru, et number paneme Number()
		
        if (piletid <= 0) {
            alert("Sisesta piletite arv!");
            event.preventDefault(); //ära navigeeri lehelt minema
            return;
        }
	}
);