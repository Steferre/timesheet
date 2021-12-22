## Aggiornato in data 21/12/2021

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
- paginazione in tutte le view
##  DA FARE
- per la paginazione verificare, che quando si torna indietro, si torni alla pagina dalla quale si è andati al dettaglio
## IPOTESI
- se un contratto non presenta ticket, quando vado a fare l'export non viene considerato,
(introdurre la regola che se un contratto non presenta ticket è in stato 'non attivo'?)
## DA AGGIUNGERE
1 opzione doppia tipologia di accumulo delle ore, a salire da 0 e ascendere dal totale
2 nella scelta dei centri di costo devo poter scegliere solo i centri di costo di alcune aziende

## RISOLUZIONE
1 agg nuovo campo enum tabella contratti:
- questo campo definirà se il contratto accumula ore, partendo da 0
- oppure se sottrae ore, partendo da un totale pattiuto in sede di stipula
2 agg nuovo campo (foreign key) nella tabella dei centri di costo, che li relaziona all'azienda cliente

## RAGIONAMENTI CON MARCELLO
oltre la data di fine il contratto si chiude automaticamente
fare diverse view per vedere i contratti, solo quelli attivi, solo quelli chiusi o tutti

## ANNOTAZIONI
per quanto riguarda la scelta del centro di costo quando si va a creare il ticket devo avere visibile tutta la lista in quanto posso selezionare un centro di costo qualsiasi (per ipotesi, se fosse il primo ticket non avrei collegamenti con questo cont)
ipotesi giornata di lavoro di 8 ore divisa in 4 interventi di 2 ore ciascuno per 4 centri di costo diversi, al termine della giornata io dovrò registrare 4 ticket ognuno di 2 ore, il pacchetto/contratto/cliente che ha stipulato questo pacchetto è sempre lo stesso.
se quando vado ad inserire i ticket ho una lista filtrata di centri di costo (MAGARI POTREI NON VEDERE UN CDC NUOVO PER CUI QUEL GIORNO HO SVOLTO IL PRIMO INTERVENTO E NON AVEVA ANCORA IL LEGAME CON IL CLIENTE/CONTRATTO)
domanda: sarebbe preferibile lasciare visibile tutta la lista dei centri di costo, quando si vanno a creare i ticket
e qualora si volesse limitarli, si potrebbe prevedere una scelta dei centri di costo quando si va a creare il cliente o il contratto?
(quando vado a creare il nuovo cliente SO GIA' I SUOI CENTRI DI COSTO? 
quando creo i contratti posso selezionare i centri di costo alla quale si possono associare ticket?)