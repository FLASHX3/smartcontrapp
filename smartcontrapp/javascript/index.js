var a  //verificateur de login
var b  //verificateur de mot de passe

function surligne(champ,erreur)
{
    champ.style.color = erreur? "red" : "";
}

function verifLogin(login)
{
    var erreur= document.getElementsByClassName('erreur');
    var regex=/^[a-zA-Z]{6,25}$/;

    if(login.value=="")
    {
		surligne(login,false);
		erreur[0].innerHTML="";
        a=false;
	}
    else
    {
        if(!regex.test(login.value))
        {
            surligne(login,true);
            a=false;
            erreur[0].innerHTML="email invalid!";
        }
        else{
            surligne(login,false);
            a=true;
            erreur[0].innerHTML="";
        }
    }
    return a;
}

function verifMdp(mdp)
{
    var erreur=document.getElementsByClassName('erreur');
    var regex=/^[a-zA-Z0-9.-_*@&$]{9}$/;

    if(mdp.value=="")
    {
        surligne(mdp,false);
        erreur[0].innerHTML="";
        b=false;
    }
    else
    {
        if(!regex.test(mdp.value))
        {
            surligne(mdp,true);
            b=false;
            erreur[0].innerHTML="Le mot de passe doit contenir 9 caractères minimum sans caractères spéciaux!";
        }
        else
        {
            surligne(mdp,false);
            b=true;
            erreur[0].innerHTML="";
        }
    }
    return b;
}

function verifForm(form)
{
    var loginOk=verifLogin(form.login);
    var mdpOk=verifMdp(form.password);

   return (loginOk && mdpOk) ? true : false;
}