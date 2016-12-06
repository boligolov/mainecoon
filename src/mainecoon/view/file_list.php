<?php
if (!empty($list))
{
    ?>
    <table class="files">
        <tr>
            <th>Name</th>
            <?php

            foreach ($attributes as $attribute => $enabled)
            {
                if ($enabled)
                {
                    ?>
                    <th><?= $attribute; ?></th>
                    <?php
                }
            }
            ?>
        </tr>
        <?php
        foreach ($list as $filename => $file)
        {
            ?>
            <tr>
                <td>
                    <?= $filename; ?>
                    <?php
                    if (!empty($file['comment']))
                    {
                        ?>
                        <small class="comment"><?= $file['comment']; ?></small>
                        <?php
                    }
                    ?>
                </td>
                <?php
                foreach ($file as $key => $value)
                {
                    if ($key != 'comment' && $attributes[$key])
                    {
                        ?>
                        <td>
                            <?= $value; ?>
                        </td>
                        <?php
                    }
                }
                ?>
            </tr>
            <?php
        }
        ?>
    </table>
    <?php
}
?>