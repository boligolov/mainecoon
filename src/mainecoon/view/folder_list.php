<ul class="folders">
    <?php
    foreach ($list as $key => $folder)
    {
        ?>
        <li id="folder-<?= $key ?>" class="folder-container">
            <div class="folder-title">
                <div class="row">
                    <div class="column column-80">
                        <div class="arrow"></div>
                        <div><?= $folder['name'] ?></div>
                    </div>
                    <div class="column column-20">
                        <div class="status"></div>
                    </div>
                </div>
            </div>
            <div class="folder-files">
                <div class="files"></div>
            </div>
        </li>
        <?php
    }
    ?>
</ul>