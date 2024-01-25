# First raw doc

Il metodo di pagamento Pagolight offre un gateway esterno. Per poter accedere a questo gateway è necessario innanzitutto autenticarsi
tramite la merchant key. Una volta ottenuto il token si può aprire un nuovo contratto. Il contratto dura 2 ore, successivamente scade.

In caso di annullamento e fallimento del pagamento da parte del gateway la risposta è una GET senza parametri alla rotta specificata in
fase di creazione del contratto.

@TODO: Non conosciamo ancora il metodo in cui il gateway risponde con successo al pagamento. Anche in questo caso
potrebbe essere una GET senza parametri alla rotta specificata in fase di creazione del contratto.

## Prima ipotesi

Ci agganciamo alla capture action. Questa genera due token: capture e after.
Il flusso rimane quello classico, ma il gateway non ritornerà in maniera diretta sulla capture.

Prima, introduciamo 2 rotte (3 in caso anche il success sia una GET). 
Queste due rotte lanceranno rispettive action molto simili alla status action per marcare come fallito o cancellato (o pagato) il pagamento.
Poi, si torna a contattare la capture che, a questo punto avrà i dettagli settati. 





TODO
- [ ] Salvare in cache il bearer token
- [ ] 
