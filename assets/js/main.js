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

