<?php
foreach ($list as $link)
{
    if (is_array($link))
    {
        ?>
        <script
        <?php
        foreach ($link as $attribute => $value)
        {
            echo $attribute.'="'.$value.'" ';
        }
        ?>
        ></script>
        <?php
    }
    else
    {
        ?>
        <script src="<?= $link; ?>"></script>
        <?
    }
}
?>