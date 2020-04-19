const urlParams = new URLSearchParams(window.location.search)
const cardContainer = document.querySelectorAll('.cards')

const priceWithCurrency = (price) => `${price.toFixed(2).toString().replace('.',',')} zł`

cardContainer.forEach((container) => {
  const cards = container.querySelectorAll('.card')

  const cardIds = [];
  cards.forEach((card) => {
    cardIds.push(card.getAttribute('for'))
  })

  let firstId
  let anyHasActive = false

  cards.forEach((card, ix) => {
    if (ix === 0) {
      firstId = card.getAttribute('for')
    }

    if (card.classList.contains('active')) anyHasActive = true

    card.addEventListener('click', () => {
      cards.forEach((idWillBeToggled) => {
        idWillBeToggled.classList.remove('active')
        document.querySelector(`.card-box#${idWillBeToggled.getAttribute('for')}`).classList.remove('active')
      })

      card.classList.add('active')
      document.querySelector(`.card-box#${card.getAttribute('for')}`).classList.add('active')
    })
  })

  if (firstId && !anyHasActive) {
    document.querySelector(`.card[for='${firstId}']`).classList.add('active')
    document.querySelector(`.card-box#${firstId}`).classList.add('active')
  }
})

var cookieConsent = document.querySelector('.cookie-consent')

function getCookie(cname) {
  var name = cname + "=";
  var decodedCookie = decodeURIComponent(document.cookie);
  var ca = decodedCookie.split(';');
  for(var i = 0; i <ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}

function setCookie(cname, cvalue, exdays) {
  var d = new Date();
  d.setTime(d.getTime() + (exdays*24*60*60*1000));
  var expires = "expires="+ d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

var cookie = getCookie("cookieConsent")

if(!cookie){
  cookieConsent.classList.remove('hidden')
  var close = cookieConsent.querySelector(".cross")
  close.onclick = function() {
    cookieConsent.classList.add('hidden')
    setCookie('cookieConsent', '1', 7)
  }
}

const priceResult = document.querySelector('p.contact-price-calculation')
if (priceResult) {
  const beginInput = document.querySelector('input[name=from]')
  const endInput = document.querySelector('input[name=to]')
  const car = document.querySelector('select#car')
  const insuranceYes = document.querySelector('input#insurance-yes')
  const insuranceNo = document.querySelector('input#insurance-no')

  const usedFields = {
    beginInput: urlParams.has('from') ? true : false,
    endInput: urlParams.has('to') ? true : false,
  }

  const calculateFinal = () => {
    if (!Object.values(usedFields).includes(false)) {
      if (!priceResult.classList.contains('show')) priceResult.classList.add('show')

      const insurance = insuranceYes.checked && !insuranceNo.checked ? true : false
      const begin = new Date(beginInput.value).getTime()
      console.log(beginInput.value, endInput.value, car.value)
      const end = new Date(endInput.value).getTime()
      const dayDifference = (end - begin) / (1000 * 3600 * 24)

      const priceSpan = parseFloat(document.querySelector(`span.data-tag[data-car='${car.value}']`).dataset.carPrice)

      const price = insurance ? priceSpan * dayDifference + 39.90 * dayDifference : priceSpan * dayDifference

      if (!isNaN(price)) {
        priceResult.querySelector('span').textContent = priceWithCurrency(price)
      }
    }
  }

  beginInput.addEventListener('change', () => {
    usedFields.beginInput = true
    calculateFinal()
  })

  endInput.addEventListener('change', () => {
    usedFields.endInput = true
    calculateFinal()
  })

  car.addEventListener('click', () => calculateFinal())
  insuranceYes.addEventListener('click', () => calculateFinal())
  insuranceNo.addEventListener('click', () => calculateFinal())

  calculateFinal()
}
