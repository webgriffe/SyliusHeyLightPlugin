const afterUrl = window.afterUrl
const paymentStatusUrl = window.paymentStatusUrl

async function refresh() {
  try {
    const response = await fetch(paymentStatusUrl);
    const data = await response.json();

    if (data.captured) {
      window.location.replace(afterUrl)
      return
    }
  } catch (e) {
    console.log(e)
  }

  setTimeout(refresh, 5000);
}

refresh();
