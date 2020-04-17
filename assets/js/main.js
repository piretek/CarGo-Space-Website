const cardContainer = document.querySelectorAll('.cards')

cardContainer.forEach((container) => {
  const cards = container.querySelectorAll('.card')

  const cardIds = [];
  cards.forEach((card) => {
    cardIds.push(card.getAttribute('for'))
  })

  cards.forEach((card, ix) => {
    if (ix === 0) {
      card.classList.add('active')
      document.querySelector(`.card-box#${card.getAttribute('for')}`).classList.add('active')
    }

    card.addEventListener('click', () => {
      cards.forEach((idWillBeToggled) => {
        idWillBeToggled.classList.remove('active')
        document.querySelector(`.card-box#${idWillBeToggled.getAttribute('for')}`).classList.remove('active')
      })

      card.classList.add('active')
      document.querySelector(`.card-box#${card.getAttribute('for')}`).classList.add('active')
    })
  })
})

