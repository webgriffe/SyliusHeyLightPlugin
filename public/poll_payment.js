
const afterUrl = window.afterUrl
let cont = 0;

function refresh() {
  cont++;
  if (cont >= 3) {
    window.location.replace(afterUrl);
  }

  setTimeout(refresh, 5000);
}

refresh();
