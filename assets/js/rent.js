const searchInput = document.querySelector('#search-for-client')
const searchResults = document.querySelector('table.search-results tbody')

const chosenSelect = document.querySelector('select#car')
chosenSelect.addEventListener('change', (e) => {

  const price = chosenSelect.querySelector(`option[value='${e.target.value}']`).getAttribute('data-price')


})

searchInput.addEventListener('input', () => {
  const { value } = searchInput

  if (value.length < 2) {
    if (!document.querySelector('table .no-results')) {
      searchResults.innerHTML = ''

      const td = document.createElement('td')
      td.setAttribute('colspan', '3')
      td.classList.add('no-results')
      td.textContent = 'Wpisz min. 2 znaki.'

      const tr = document.createElement('tr')
      tr.appendChild(td)
      searchResults.appendChild(tr)
    }
  }
  else {
    searchResults.innerHTML = ''

    const foundIndexes = []
    const searchables = value.split(' ')

    searchables.forEach((searchable) => {
      clients.forEach((client, index) => {
        Object.keys(client).forEach((key) => {
          if (key !== 'id' && searchable !== '') {
            if (client[key].toLowerCase().search(searchable.toLowerCase()) !== -1 && !foundIndexes.includes(index)) {
              foundIndexes.push(index)
            }
          }
        })
      })
    })

    if (foundIndexes.length == 0) {
      const td = document.createElement('td')
      td.setAttribute('colspan', '3')
      td.classList.add('no-results')
      td.textContent = 'Brak wyników... Utwórz nowego klienta.'

      const tr = document.createElement('tr')
      tr.appendChild(td)
      searchResults.appendChild(tr)
    }
    else {
      foundIndexes.forEach((index) => {
        const { id, name, surname, pesel } = clients[index]

        const tdName = document.createElement('td')
        tdName.textContent = `${surname} ${name}`

        const tdPesel = document.createElement('td')
        tdPesel.textContent = pesel

        const tdBttn = document.createElement('td')
        tdBttn.textContent = 'Wybierz'
        tdBttn.addEventListener('click', () => {
          document.querySelector('#search').classList.add('chosen')
          document.querySelector('p.search-results span').textContent = `${surname} ${name} (${pesel})`
          document.querySelector('p.search-results span').textContent = `${surname} ${name} (${pesel})`
          document.querySelector('input#client-id').value = id
          document.querySelector('input#search-for-client').value = ''
        })

        const tr = document.createElement('tr')
        tr.setAttribute('data-id', id)
        tr.appendChild(tdName)
        tr.appendChild(tdPesel)
        tr.appendChild(tdBttn)

        searchResults.appendChild(tr)
      })
    }
  }
})
