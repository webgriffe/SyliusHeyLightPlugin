# First raw doc

> Sorry, this is a raw doc. It will be translated in english soon.

Il metodo di pagamento Pagolight offre un gateway esterno. Per poter accedere a questo gateway è necessario innanzitutto autenticarsi
tramite la merchant key. Una volta ottenuto il token si può aprire un nuovo contratto. Il contratto dura 2 ore, successivamente scade.

In caso di annullamento e fallimento del pagamento da parte del gateway la risposta è una GET senza parametri alla rotta specificata in
fase di creazione del contratto.

@TODO: Non conosciamo ancora il metodo in cui il gateway risponde con successo al pagamento. Anche in questo caso
potrebbe essere una GET senza parametri alla rotta specificata in fase di creazione del contratto.


TODO
- [x] Salvare in cache il bearer token
- [x] Creare delle Payum API che siano univoche per ogni gateway normale/PRO
- [ ] Aggiungere regola di validazione univocità gateway normale/PRO
- [ ] Completare il contract
- [ ] Aggiungere il webhook
- [ ] Valutare pagina di stato con JS che pinga il server
- [ ] Documentazione
- [ ] A cosa corrispondono i vari stati della transazione di Pagolight?
- [ ] Se annullo il checkout lo stato è pending?? Come gestiamo il caso? Annulliamo il pagamento?
- [ ] Riprendendo la URL ripartiamo dall'ultimo stato? O ricomincia?
- [ ] Il webhook di successo parte subito? O dopo un tot di tempo?
