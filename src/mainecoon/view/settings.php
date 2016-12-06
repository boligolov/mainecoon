
<form action="/mainecoon.php" method="POST">
  <input type="hidden" name="action" value="settings"/>
  <fieldset>
    <legend><?= Lang::t('settings/legend/general'); ?></legend>
    <label for="config_write">Сохранять конфигурацию в файл .mainecoon</label>
    <input type="checkbox" name="config_write" value="1" id="config_write"/>
    <label for="test_requirements">Тестировать среду на соответсвованияе требованиям</label>
    <input type="checkbox" name="test_requirements" value="1" id="test_requirements"/>
    <label for="test_requirements_on_ajax">Тестировать среду на соответсвованияе требованиям при AJAX-запросах</label>
    <input type="checkbox" name="test_requirements_on_ajax" value="1" id="test_requirements_on_ajax"/>
    <label for="debug">Включить отладку</label>
    <input type="checkbox" name="debug" value="1" id="debug"/>
    <label for="language">Язык по-умолчанию</label>
    <select name="language" id="language">
      <option value="ru">русский</option>
      <option value="en">английский</option>
    </select>
  </fieldset>
  <fieldset>
    <legend><?= Lang::t('settings/legend/temp'); ?></legend>
    <label for="temp_dir">Имя временного каталога</label>
    <input type="text" name="temp_dir" id="temp_dir" value="mainecoon_temp"/>
    <label for="temp_filename">Имя временного файла с общей информацией</label>
    <input type="text" name="temp_filename" id="temp_filename" value="mainecoon.temp"/>
    <label for="temp_flag">Имя временного файла-флага</label>
    <input type="text" name="temp_flag" id="temp_flag" value="mainecoon.flag"/>
    <label for="temp_dir_create">Пытаться создать каталог, если не существует</label>
    <input type="checkbox" name="temp_dir_create" value="1" id="temp_dir_create" checked="checked"/>
    <label for="temp_dir_clear_before">Очищать временный каталог от файлов перед работой</label>
    <input type="checkbox" name="temp_dir_clear_before" value="1" id="temp_dir_clear_before" checked="checked"/>
  </fieldset>
  <fieldset>
    <legend><?= Lang::t('settings/legend/exclude'); ?></legend>
    <label for="exclude_path">Маски путей</label>
    <textarea name="exclude[path]" id="exclude_path">.git
.idea
nbproject
.buildpath
.project
.settings
.DS_Store
.vagrant</textarea>
    <label for="exclude_ext">Расширения для исключения</label>
    <textarea name="exclude[ext]" id="exclude_ext">jpg
jpeg
png
bmp
gif
mp4
mp3
ogg</textarea>
    <label for="exclude_mime">Mime для исключения</label>
    <textarea name="exclude[mime]" id="exclude_mime"></textarea>
    <label for="exclude_size_more">Максимальный размер файла</label>
    <input type="text" name="exclude[size_more]" id="exclude_size_more" value="1048576"/>
    <label for="exclude_size_less">Максимальный размер файла</label>
    <input type="text" name="exclude[size_less]" id="exclude_size_less" value="0"/>
  </fieldset>
  <fieldset>
    <legend><?= Lang::t('settings/legend/params'); ?></legend>
    <input type="checkbox" name="check[md5]" value="1" id="check_md5" checked="checked"/>
    <label for="check_md5">MD5</label>
    <input type="checkbox" name="check[mimetype]" value="1" id="check_mimetype" checked="checked"/>
    <label for="check_mimetype">Mime Type</label>
    <input type="checkbox" name="check[size]" value="1" id="check_size" checked="checked"/>
    <label for="check_size">Size</label>
    <input type="checkbox" name="check[rights]" value="1" id="check_rights" checked="checked"/>
    <label for="check_rights">Rights</label>
    <input type="checkbox" name="check[mtime]" value="1" id="check_mtime" checked="checked"/>
    <label for="check_mtime">MTime</label>
    <input type="checkbox" name="check[atime]" value="1" id="check_atime" checked="checked"/>
    <label for="check_atime">ATime</label>
    <input type="checkbox" name="check[ctime]" value="1" id="check_ctime" checked="checked"/>
    <label for="check_ctime">CTime</label>
  </fieldset>
</form>