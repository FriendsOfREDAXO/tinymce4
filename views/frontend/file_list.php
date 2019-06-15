<div class="file-list">
    <?php if (0 == $offset && 0 == count($link_list)) : ?>
        <div class="alert alert-info">
            Keine gefunden
        </div>
    <?php endif; ?>

    <?php foreach ($link_list as $link): ?>
        <li class="list-group-item">
            <a href="" onclick="returnFile(this)"
			<?php if(rex_get('tinymce4_call', 'string', '') == '/image/index' && $link['filetype'] == 'image/jpeg'){ ?>
			data-value="<?php
			if (in_array(\rex_config::get('tinymce4', 'image_format'), ['default', ''])) {
				echo 'index.php?rex_media_type=tinymcewysiwyg&rex_media_file='.urlencode($link['filename']);
			} else {
				echo str_replace('{filename}', urlencode($link['filename']), \rex_config::get('tinymce4', 'image_format'));
			}
			?>"
			<?php } else { ?>
               data-value="<?= rex_extension::registerPoint(new rex_extension_point('TINYMCE_FILELIST_URL', $link['url'], [
                   'type' => $type,
                   'item' => $link,
               ])) ?>"
			<?php } ?>			
            ><?php echo $link['name']; ?></a>
        </li>
    <?php endforeach; ?>
    <?php if ($total > $offset + $limit): ?>
        <br/>
        <a class="btn btn-default btn-block"
           href="<?php echo $UrlService->getAjaxUrl('/file/index', [
               'ofl'         => 1,
               'page'        => $page + 1,
               'type'        => $type,
               'category_id' => $category_id,
               'clang_id'    => $clang_id,
               'search'      => $search,
               'ts'          => time(),
           ]); ?>" onclick="return loadMore(this)">Mehr</a>
        <br/>
        <br/>
    <?php endif; ?>
</div>
