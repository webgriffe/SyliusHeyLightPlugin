# Decision record template by Michael Nygard

# Flusso pagamento Heylight

Abbiamo deciso di utilizzare il webhook come unico metodo per conoscere il metodo di pagamento.
Per fare questo sono necessarie le seguenti operazioni:

- [x] La cancel url deve essere la cancel action di Payum per essere certi che il pagamento venga cancellato in maniera
  istantanea
- [x] La capture action non si occuperà più di chiedere lo stato dell'esito del pagamento, ma mostrerà una pagina che
  conterrà un JS
  che invierà una richiesta in polling a una rotta custom che dirà se il pagamento è stato catturato. Quando il
  pagamento è stato catturato allora
  farà un redirect automatico alla rotta after url del token (?).
- [x] La rotta custom che viene chiamata in polling deve essere in grado di capire se il pagamento è stato catturato o meno
  e di ritornare un json con il risultato
- [x] Deve essere implementata la notify action che verrà chiamata dal webhook di heylight e che si occuperà di aggiornare
  lo stato del pagamento
