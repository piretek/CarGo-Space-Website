const cardContainer = document.querySelectorAll('.cards')

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
