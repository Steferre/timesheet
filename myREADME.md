## Aggiornato in data 20/12/2021

## contracts views
index  ok
create ok
edit ok
show ok
bottone delete e info, se non ci sono ticket legati al contratto allora è visibile il cestino e si potrebbe eliminare il contratto stesso, se, invece, è presente almeno un ticket, appare un icona info, che se cliccata farà apparire un messaggio esplicativo.
bottone + da la possibilità di aggiungere un ticket allo specifico contratto, cliccandolo si viene mandati nella create dei tickets, ma i campi contratto, azienda cliente e azienda del gruppo sono già selezionate.

## contracts controller
ok, tutte le funzioni base della crud sono attive e portano alla pagina di riferimento.

## clients views
index ok
create ok
edit ok
show quando sono normale user DEVO vedere solo i contratti che la mia azienda ha aperto con le aziende clienti

## clients controller
la funzione store fa il redirect alla pagina index, valutare di fare il redirect a show del singolo cliente


## COMPITI SVOLTI
- controlli validità date
validità date, nel create dei contratti ho messo la validazione tramite js (come esempio)
nella create dei tickets ho messo la validazione tramite laravel e le sue regole
- controlli validità ore
fatte con le regole di laravel
- campo ore extra per l'amministratore
aggiunto campo ore extra e modificata query che calcola le ore e le mette in pagina nella lista contratti
sembra funzionare tutto
- aggiungere il controllo della fine delle ore alla create di un ticket
- aggiungere sezione per effettuare il filtro nella zona dei contratti
per azienda cliente e/o per società del gruppo lato admin
per azienda cliente lato user
- aggiungere sezione per effettuare il filtro nella zona delle aziende
per nome azienda (solo lato admin in quanto l'user non vede le aziende clienti)
- aggiungere sezione per effettuare il filtro nella zona dei centri di costo
per nome centro di costo (solo lato admin in quanto l'user non vede le aziende clienti)
- aggiungere sezione per effettuare il filtro nella zona dei ticket
per nome contratto, per range di data, per azienda cliente lato admin e lato user
- aggiunto anche filtro per centri di costo
- continuare a riscrivere la funzione index del ContractController per il caso dell'user, con lo schema usato per la funione index del TicketController
- filtro data in range, che lavora sulla data chiusura
- elenco contratti filtrati per cliente(es lyve)
- nello show di un contratto vedo anche i ticket legati a quel contratto e volendo potrei esportarli
- nel file excel l'amministratore azienda gruppo azienda cliente contratto di riferimento e ticket legati al contratto
- se un'azienda compra più di un pacchetto e i contratti che apre sono più di uno, bisgona fare in modo che gli user possano alimentare solo i contratti che sono attivi
##  DA FARE (EXPORT)
## IPOTESI
- se un contratto non presenta ticket, quando vado a fare l'export non viene considerato,
(introdurre la regola che se un contratto non presenta ticket è in stato 'non attivo'?)
## DA AGGIUNGERE
- paginazione in tutte le view

