
<div class="row main-buttons">
  <div class="column column-50">
    <form action="/mainecoon.php" method="post" id="action-form-1">
      <input type="hidden" name="action" value="snapshot"/>
      <button class="button-snapshot" type="submit">Сделать снимок</button>
    </form>
    <form action="/mainecoon.php" method="post" id="action-form-3">
      <input type="hidden" name="action" value="result"/>
      <button class="button-snapshot" type="submit">Сохранить снимок</button>
    </form>
  </div>
  <div class="column column-50">
    <form action="/mainecoon.php" method="post" id="action-form-2" enctype="multipart/form-data">
      <input type="hidden" name="action" value="comparsion"/>
      <input type="file" name="file"/>
      <button class="button-comparsion" type="submit">Выполнить сравнение</button>
    </form>
  </div>
</div>