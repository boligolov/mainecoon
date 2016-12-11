<!DOCTYPE html>
<html>
  <head><?= $header; ?></head>
  <body>
    <header>
      <div class="container">
        <div class="row header">
          <div class="column column-20">
            <h3>Mainecoon</h3>
          </div>
          <div class="column"><span class="control--toggle-all" data-minimized="">Свернуть всё</span></div>
          <div class="column column-10">
            <div class="server-info"><i class="fa fa-info" aria-hidden="true"></i>
              <div class="server-info-block"></div>
            </div>
            <div class="settings"><i class="fa fa-cog" aria-hidden="true"></i>
              <div class="settings-block"></div>
            </div>
          </div>
        </div>
      </div>
      <div id="progress-bar"></div>
    </header>
    <div class="content">
      <div class="container">
        <div class="row">
          <div class="column">
            <div class="logs"></div>
          </div>
        </div>
        <div class="row">
          <div class="column">
            <div class="dirlist"></div>
            <div id="content"><?= $content; ?></div>
          </div>
        </div>
      </div>
    </div>
    <footer><?= $footer; ?></footer>
  </body>
</html>