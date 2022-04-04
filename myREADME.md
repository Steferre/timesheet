## Aggiornato in data 20/01/2022

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
## IPOTESI
- se un contratto non presenta ticket, quando vado a fare l'export non viene considerato,
(introdurre la regola che se un contratto non presenta ticket è in stato 'non attivo'?)
## DA AGGIUNGERE (FATTO)
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
per quanto riguarda la scelta del centro di costo quando si va a creare il ticket devo avere visibile tutta la lista in quanto posso selezionare un centro di costo qualsiasi.
ipotesi giornata di lavoro di 8 ore divisa in 4 interventi di 2 ore ciascuno per 4 centri di costo diversi, al termine della giornata io dovrò registrare 4 ticket ognuno di 2 ore, il pacchetto/contratto/cliente è sempre lo stesso.
se quando vado ad inserire i ticket ho una lista filtrata di centri di costo (MAGARI POTREI NON VEDERE UN CDC NUOVO PER CUI QUEL GIORNO HO SVOLTO IL PRIMO INTERVENTO E NON AVEVA ANCORA IL LEGAME CON IL CLIENTE/CONTRATTO)
domanda: sarebbe preferibile lasciare visibile tutta la lista dei centri di costo, quando si vanno a creare i ticket
e qualora si volesse limitarli, si potrebbe prevedere una scelta dei centri di costo quando si va a creare il cliente o il contratto?
(quando vado a creare il nuovo cliente SO GIA' I SUOI CENTRI DI COSTO? 
quando creo i contratti posso selezionare i centri di costo alla quale si possono associare ticket?)

## AGGIUNTE IN DATA 28-12-2021
modificata relazione tra azienda cliente e centri di costo, ora in fase di creazione di una nuova azienda cliente, si devono scegliere i centri di costo associati.
questo ci permette tramite js e una chiamata axios di mostrare la lista dei centri di costo ai quali si possono associare ticket, non appena viene scelto il contratto sul quale aprire il ticket

## DA CONTROLLARE E MODIFICARE
controllare il comportamento quando si arriva alla creazione di un nuovo ticket direttamente dal contratto (RISOLTO)
controllare comportamento quando ci sono più contratti tra i quali scegliere, se la lista dei cdc si svuota o meno (RISOLTO)
quando inserisco una nuova azienda cliente controllare se esiste già come centro di costo prima di inserirla anche come cdc (RISOLTO)

controllare validazione date sulla creazione dei ticket, se si mette una data precedente all'inizo del contratto ti fa aprire lo stesso il ticket (RISOLTO)

## CRITICITA' RISCONTRATE DA CORRADO 
Creazione azienda cliente, mi ha richiesto alcuni campi obbligatori e altri opzionali che ho lasciato vuoti
Nel form di inserimento mi mostra due checkbox (centri di costo); manca una label come invece è presente nel form edit
Mi permette di salvare ma dopo aver salvato mostra un errore (screen1). Ciò nonostante il cliente risulta creato e anche il centro di costo con lo stesso nome (RISOLTO)
 
Vado in edit dell’azienda cliente; i campi opzionali non sono più tali e sono diventati obbligatori, per cui sono vincolato a doverli aggiornare; il comportamento dei due form non è coerente (insert/edit).
Anche qui sono presenti i centri di costoquesta volta con la label. (RISOLTO)

Inserimento ticket:
In alto a sx è presente il testo “bool(false)” immagino sia un debug da togliere
l’elenco delle opzioni “eseguito” mi mostra due volte lo stesso utente (Screen 2)
descrizione dell’intervento. Metterei il campo sulla riga successiva e aumenterei il numero di colonne che può occupare, in modo che per descrizioni più dettagliate sia visibile tutto il testo evitando uno scroll manuale (RISOLTO)

elenco ticket:
per ottimizzare lo spazio toglierei la colonna “aperto” e aumenterei lo spazio a disposzione del commento, per avere già in questo elenco un colpo d’occhio rispetto all’intervento.
Ho eseguito una ricerca per azienda cliente selezionando dalle opzioni (screen_3), selezionando un’azienda che non ha ticket.  Lo screen con i risultati non mostra la selezione che ho fatto nelle options, mostra il messaggio , mostra il messaggio che non ci sono risultati (corretto) ma la table contiene lo stesso il ticket di lyve (che non dovrebbe mostrare). (RISOLTO)

## DUBBI
nella funzione store del ticketController, la validazione della data dei ticket
- in apertura deve essere antecedente o al massimo uguale alla data odierna
- in chiusura deve essere ??? (attualmente è possibile inserire una data uguale o successiva a quella di apertura) : (ma forse sarebbe meglio mettere un range più ristretto...tipo strettamente uguale ad oggi) => (quando si crea il ticket la data di chiusura non va selezionata, è impostata di default tramite un campo hidden a today)
- controllare che la funzione di delete del ticket funzioni (forse va gestita la situazione dei centri costo correlati, va eliminata la relazione prima di poter cancellare il ticket)
- gli user si registreranno con l email aziendale? (se si dovrebbe andare bene così) : (in caso contrario va cambiata la logica per gli user)

impostare di default che le date del ticket sono fissate ad oggi(FATTO)
##  DA FARE
- quando si è loggati come admin, impostare che, se il campo workTime viene inviato vuoto, viene messo 0 come default (FATTO)
- controllare il riempimento del campo contratto e cliente quando si viene riportati alla pagina di creazione del ticket a seguito di un errore (FATTO)
- scrivere funzione di eliminazione dei centri di costo(FATTO)

eliminare required da campo descrizione(FATTO)

## TO DO
- mostrare ticket in ordine decrescente dall'ultimo creato
- riguardare errore dovuto al codice del contratto