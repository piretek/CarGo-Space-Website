document.addEventListener('DOMContentLoaded', function() {

  const urlParams = new URLSearchParams(window.location.search)
  const pages = document.querySelectorAll('.page')
  const pageButtons = document.querySelectorAll('.page-bttn')

  function changePage(id, title) {
    pages.forEach((page) => {
      page.classList.remove('active')
    })

    document.querySelector(`#page-${id}`).classList.add('active')
    document.querySelector(`.page--title`).innerHTML = title
  }

  pageButtons.forEach((pageButton) => {
    pageButton.addEventListener('click', function() {
      if (urlParams.has('view')) urlParams.delete('view')

      if (urlParams.has('action')) {
        window.location.href = site_url + '/dashboard.php?view=' + pageButton.getAttribute('id')
      }
      else {
        changePage(pageButton.getAttribute('id'), pageButton.textContent)
      }
    })
  })


  if (!urlParams.has('view') || urlParams.get('view') === 'rents') {
    changePage('rents', 'Wypożyczenia')
  }
  else if (urlParams.get('view') === 'fleet') {
    changePage('fleet', 'Flota')
  }
  else if (urlParams.get('view') === 'users') {
    changePage('users', 'Użytkownicy')
  }
  else if (urlParams.get('view') === 'account') {
    changePage('account', 'Twoje konto')
  }
})