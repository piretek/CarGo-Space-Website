function showImage( newImage ) {

  document.querySelectorAll('.background.showing').forEach(function (background) {
    background.classList.remove('showing');
  })

  const background = document.querySelector(`#background-${newImage}`)
  if (background) background.classList.add('showing');
}

document.addEventListener('DOMContentLoaded', function() {

  let showingImage = 0;
  const backgrounds = document.querySelectorAll('.background')

  const time = 3000;
  const bttnLeft = document.querySelector('.bg-left');
  const bttnRight = document.querySelector('.bg-right');

  if (bttnLeft) {
    bttnLeft.addEventListener('click', function () {
      if (showingImage == 0) {
        showingImage = backgrounds.length - 1
      }
      else {
        showingImage -= 1
      }

      showImage(showingImage)
    })
  }

  if (bttnRight) {
    bttnRight.addEventListener('click', function () {
      if (showingImage == (backgrounds.length - 1)) {
        showingImage = 0
      }
      else {
        showingImage += 1
      }

      showImage(showingImage)
    })
  }

  setInterval(function() {
    if (showingImage == (backgrounds.length - 1)) {
      showingImage = 0
    }
    else {
      showingImage += 1
    }

    showImage(showingImage)
  }, time)
})
