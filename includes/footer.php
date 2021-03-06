<?php

if (!defined('SECURE_BOOT')) exit();

?>

    <div class="footer <?= basename($_SERVER['PHP_SELF']) == 'about.php' ? 'rm-margin' : '' ?>">
      <div class="footer-h">
        <h1>CarGo Space*</h1>
      </div>
      <div class="footers">
        <div class="footer-1">
          <h2>Realizacja</h2>
            Piotr Czarnecki, Michał Kowalski i Paulina Sznajder
            <br>
            z klasy 3ft.
            <div class="dark-mode-button">
              <form class="as-anchor dont-ask" method="post">
                <input type="hidden" name="action" value="dark-mode-toggle">
                <button type="submit">Przełącz na tryb <?= isset($_COOKIE['dark-mode']) && $_COOKIE['dark-mode'] == 1 ? 'jasny' : 'ciemny' ?></button>
              </form>
            </div>
        </div>
        <div class="footer-2">
          <h2>Wynajem aut</h2>
          Wybierz samochód, ktory najbardziej Ci odpowiada<br>
          i wynajmij go u nas już dziś - w niskiej cenie!
        </div>
        <div class="footer-3">
          <h2>Uwaga</h2>
          *Pomimo wielu wariantów aut jakie posiadamy,<br>
          samochody z naszej floty NIE latają w kosmos (jeszcze...).
        </div>
      </div>
      <div class="footer-c">
        Copyright &copy; 2020 CarGo Space Polska S.A. Wszelkie prawa zastrzeżone.
      </div>
    </div>
  </div>
</div>
<div class="cookie-consent hidden">
  <span class='cookie-info'>Ta strona korzysta z ciasteczek aby świadczyć usługi na najwyższym poziomie. Dalsze korzystanie ze strony oznacza, że zgadzasz się na ich użycie.</span>
  <span class="cross"></span>
</div>
<script src='assets/js/main.js'></script>
</body>
</html>
