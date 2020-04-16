const selectors = document.querySelectorAll('.car-selector')
const selected = document.querySelector('.selected-car')

const beginInput = document.querySelector('input[name=begin]')
const endInput = document.querySelector('input[name=end]')

let carData = {}

const elementsFilled = {
  car: false,
  begin: true,
  end: false
}

const calculate = () => {

  if (!Object.values(elementsFilled).includes(false)) {
    const priceWithCurrency = (price) => `${price.toFixed(2).toString().replace('.',',')} zł`

    const costContainer = document.querySelector('.costs')
    costContainer.innerHTML = ''

    const price = parseFloat(carData.price)

    const begin = new Date(beginInput.value).getTime()
    const end = new Date(endInput.value).getTime()

    const beginError = document.querySelector('.input-id--begin span.input--error')
    const endError = document.querySelector('.input-id--end span.input--error')

    const dayDifference = (end - begin) / (1000 * 3600 * 24)
    let costs = {
      costPerOne: {},
      days: {},
      costNetto: {},
      costVat: {},
      costBrutto: {},
    }

    if (dayDifference > 0) {
      beginError.textContent = ''
      endError.textContent = ''

      costs.costPerOne.label = document.createElement('strong')
      costs.costPerOne.label.textContent = 'Cena za 1 dzień (brutto): '
      costs.costPerOne.text = document.createTextNode(priceWithCurrency(price))

      costs.days.label = document.createElement('strong')
      costs.days.label.textContent = 'Dni w cenie: '
      costs.days.text = document.createTextNode(dayDifference)

      costs.costNetto.label = document.createElement('strong')
      costs.costNetto.label.textContent = 'Cena netto: '
      costs.costNetto.text = document.createTextNode(priceWithCurrency(price * dayDifference * 0.77))

      costs.costVat.label = document.createElement('strong')
      costs.costVat.label.textContent = 'VAT (23%): '
      costs.costVat.text = document.createTextNode(priceWithCurrency(price * dayDifference * 0.23))

      costs.costBrutto.label = document.createElement('strong')
      costs.costBrutto.label.textContent = 'Cena brutto: '
      costs.costBrutto.text = document.createTextNode(priceWithCurrency(price * dayDifference))

      Object.values(costs).forEach((cost) => {
        const costWrapper = document.createElement('p')

        costWrapper.appendChild(cost.label)
        costWrapper.appendChild(cost.text)

        costContainer.appendChild(costWrapper)
      })

      const anchor = document.createElement('a')
      anchor.setAttribute('href', `contact.php?car=${carData.id}&from=${beginInput.value}&to=${endInput.value}`)
      anchor.textContent = 'Wynajmij to auto'
      anchor.classList.add('rent-a-car')

      costContainer.appendChild(anchor)
    }
    else {
      beginError.textContent = 'Data się nie zgadza.'
      endError.textContent = 'Data się nie zgadza.'
    }
  }
}

beginInput.addEventListener('input', () => {
  elementsFilled.begin = true
  calculate()
})

endInput.addEventListener('input', () => {
  elementsFilled.end = true
  calculate()
})

selectors.forEach((selector) => {
  selector.addEventListener('click', (e) => {
    selectors.forEach((node) => (node.classList.contains('selected') ? node.classList.remove('selected') : null))

    selector.classList.add('selected')
    elementsFilled.car = true

    carData = {
      id: selector.querySelector('input[name=car]').value,
      brand: selector.querySelector('span.car-brand').textContent,
      model: selector.querySelector('span.car-model').textContent,
      type: selector.querySelector('span.car-type').textContent,
      engine: selector.querySelector('span.car-engine').textContent,
      fuel: selector.querySelector('span.car-fuel').textContent,
      clutch: selector.querySelector('span.car-clutch').textContent,
      price: selector.querySelector('input[name=price]').value
    }

    if (selector.querySelector('img')) carData.image = selector.querySelector('img').src

    selected.innerHTML = ''

    const container = document.createElement('div')
    container.classList.add('sc--container')

    if (carData.image) {
      const imageContainer = document.createElement('div')
      imageContainer.classList.add('sc--image')

      const image = document.createElement('img')
      image.setAttribute('src', carData.image)
      image.setAttribute('alt', 'Wybrany pojazd')

      imageContainer.appendChild(image)
      container.appendChild(imageContainer)
    }

    const contentContainer = document.createElement('div')
    contentContainer.classList.add('sc--content')

    const brandModel = document.createElement('h4')
    brandModel.textContent = `${carData.brand} ${carData.model}`

    contentContainer.appendChild(brandModel)

    const content = document.createElement('ul')

    Object.keys(carData).forEach((key) => {

      const elem = document.createElement('li')
      const elemText = document.createTextNode(carData[key])
      const elemName = document.createElement('strong')

      switch( key ) {
        case 'type' :
          elemName.textContent = 'Typ: '
          break;
        case 'engine' :
          elemName.textContent = 'Silnik: '
          break;
        case 'fuel' :
          elemName.textContent = 'Paliwo: '
          break;
        case 'clutch' :
          elemName.textContent = 'Skrzynia: '
          break;
      }

      elem.appendChild(elemName)
      elem.appendChild(elemText)

      const disallowed = ['id', 'brand', 'model', 'image', 'price']

      if (!disallowed.includes(key)) content.appendChild(elem)
    })

    contentContainer.appendChild(content)
    container.appendChild(contentContainer)

    selected.appendChild(container)

    calculate()
  })
})

