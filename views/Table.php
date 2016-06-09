<?php
use Foolz\FoolFuuka\Model\CommentBulk;
use Foolz\FoolFuuka\Model\Media;
?>
<article class="thread">
<div id="table" style="padding-top:50px">
<center>
<table class="flashListing">
<tbody>
<tr>
<th class="postblock">Thread</th>
<th class="postblock">Name</th>
<th class="postblock">File</th>
<th class="postblock">Tag</th>
<th class="postblock">Subject</th>
<th class="postblock">Size</th>
<th class="postblock">Date</th>
<th class="postblock">Replies</th>
<th class="postblock"></th>
</tr>
<?php
foreach ($board as $k => $p_bulk) :
    $p = new Comment($this->getContext(), $p_bulk);
    if ($p_bulk->media !== null) {
        $p_media = new Media($this->getContext(), $p_bulk);
    } else {
        $p_media = null;
    }
?>
<tr>
<td class="no">
<a href="<?= $this->uri->create($radix->shortname . '/thread/' . $p->num) . '#' . $p->num ?>" data-function="highlight" data-post="<?= $p->num ?>"><?= $p->num ?></a>
</td>
<td class="postername">
<span class="post_author">
<?= (($p->email && $p->email !== 'noko') ? '<a href="mailto:' . rawurlencode($p->email) . '">' . ((mb_strlen($p->getNameProcessed()) > 21) ? '<span class="post_postername" rel="tooltip" title="'.  htmlspecialchars($p->getNameProcessed()) . '">' . mb_substr($p->getNameProcessed(), 0, 15, 'utf-8') . ' (...)</span>' : $p->getNameProcessed()) . '</a>' : /* $p->getNameProcessed()*/
    ((mb_strlen($p->getNameProcessed()) > 21) ? '<span class="post_postername" rel="tooltip" title="'.  htmlspecialchars($p->getNameProcessed()) . '">' . mb_substr($p->getNameProcessed(), 0, 15, 'utf-8') . ' (...)</span>' : $p->getNameProcessed())) ?></span>
    <span class="post_trip"><?= $p->getTripProcessed() ?></span>
    <span class="poster_hash"><?= ($p->getPosterHashProcessed()) ? 'ID:' . $p->getPosterHashProcessed() : '' ?></span>
    <?php if ($p->capcode == 'M') : ?>
        <span class="post_level post_level_moderator">## <?= _i('Mod') ?></span>
    <?php endif ?>
    <?php if ($p->capcode == 'A') : ?>
        <span class="post_level post_level_administrator">## <?= _i('Admin') ?></span>
    <?php endif ?>
    <?php if ($p->capcode == 'D') : ?>
        <span class="post_level post_level_developer">## <?= _i('Developer') ?></span>
    <?php endif ?>
</td>
<td class="filename">
    <?php if ($p_media !== null) : ?>
        <?php if ($p_media->getMediaStatus($this->getRequest()) === 'banned') : ?>File banned
        <?php elseif ($p_media->getMediaStatus($this->getRequest()) !== 'normal') : ?>
        <?php else: ?>
        <a href="<?= ($p_media->getMediaLink($this->getRequest())) ? $p_media->getMediaLink($this->getRequest()) : $p_media->getRemoteMediaLink($this->getRequest()) ?>" target="_blank">
            <?php if (mb_strlen($p_media->getMediaFilenameProcessed()) > 30) : ?>
                <span class="post_file_filename" rel="tooltip" title="<?= htmlspecialchars($p_media->media_filename) ?>">
                <?= mb_substr($p_media->getMediaFilenameProcessed(), 0, 24, 'utf-8') . ' (...)' . mb_substr($p_media->getMediaFilenameProcessed(), mb_strrpos($p_media->getMediaFilenameProcessed(), '.', 'utf-8'), null, 'utf-8') ?></span></a>
                <a href="<?= $p_media->getMediaDownloadLink($this->getRequest()) ?>" download="<?= $p_media->getMediaFilenameProcessed() ?>" class="btnr parent pull-right"><i class="icon-download-alt"></i></a>
            <?php else: ?>
                <?= $p_media->getMediaFilenameProcessed() ?></a>
                <a href="<?= $p_media->getMediaDownloadLink($this->getRequest()) ?>" download="<?= $p_media->getMediaFilenameProcessed() ?>" class="btnr parent pull-right"><i class="icon-download-alt"></i></a>
	        <?php endif;
	    endif;
    endif; ?>
</td>
<td class="tag">
<?php if (substr($p_media->getMediaFilenameProcessed(), -3) == "swf") : 
	if ($p_media->exif) :
	    $exiftemp = json_decode($p_media->exif) ?><span class="post_file_tag" rel="tooltip" title="Search for files with this tag">
        <a href="<?= $this->uri->create($radix->shortname . '/search/tag/' . $exiftemp->{'Tag'}) ?>"><?= $exiftemp->{'Tag'} ?></a></span>
    <?php else: ?>
        <span class="post_file_tag" rel="tooltip" title="This tag is empty for some reason">Empty</span>
	<?php endif; ?>
<?php endif; ?>
</td>
<td class="subject">
    <?php if ($p->getTitleProcessed() == ''): ?>
        <?php if (mb_strlen($p->getCommentProcessed()) > 25) : ?>
            <span class="subject" rel="tooltip" title="<?= htmlspecialchars(strip_tags($p->getCommentProcessed())) ?>">
                <?= mb_substr(htmlspecialchars(strip_tags($p->getCommentProcessed())), 0, 19, 'utf-8') . ' (...)' ?>
            </span>
        <?php else: ?>
            <?= $p->getCommentProcessed() ? htmlspecialchars(strip_tags($p->getCommentProcessed())) : '' ?>
        <?php endif; ?>
    <?php else: ?>
        <?php if (mb_strlen($p->getTitleProcessed()) > 25) : ?>
            <span class="subject" rel="tooltip" title="<?= htmlspecialchars($p->getTitleProcessed()) ?>">
                <?= mb_substr($p->getTitleProcessed(), 0, 19, 'utf-8') . ' (...)' ?>
            </span>
        <?php else: ?>
		    <?= $p->getTitleProcessed() ?>
	    <?php endif; ?>
    <?php endif; ?>
</td>
<td class="filesize">
    <?php if ($p_media !== null) : ?><?= \Rych\ByteSize\ByteSize::formatBinary($p_media->media_size, 0) ?><?php endif; ?>
</td>
<td class="date">
<time datetime="<?= gmdate(DATE_W3C, $p->timestamp) ?>" class="show_time" <?php if ($p->radix->archive) : ?> title="<?= _i('4chan Time') . ': ' . $p->getFourchanDate() ?>"<?php endif; ?>><?= gmdate('D d M H:i:s Y', $p->timestamp) ?></time>
</td>
<td class="replies">
    <?php if (isset($p->comment->nreplies)) : ?>
    <?= ($p->nreplies - 1) ?><?php endif; ?>
</td>
<td class="thread">
<a href="<?= $this->uri->create($radix->shortname . '/thread/' . $p->num) ?>" class="btnr parent"><?= _i('Reply') ?></a>
    <?= (isset($p->count_all) && $p->count_all > 50) ? '<a href="' . $this->uri->create($radix->shortname . '/last/50/' . $p->num) . '" class="btnr parent">' . _i('Last 50') . '</a>' : '' ?>
    <?php if ($radix->archive == 1) : ?>
        <a href="http://boards.4chan.org/<?= $radix->shortname . '/thread/' . $p->num ?>" class="btnr parent"><?= _i('Original') ?></a>
    <?php endif; ?>
    <a href="<?= $this->uri->create($radix->shortname . '/report/' . $p->doc_id) ?>" class="btnr parent" data-function="report" data-post="<?= $p->doc_id ?>" data-post-id="<?= $p->num ?>" data-board="<?= htmlspecialchars($p->radix->shortname) ?>" data-controls-modal="post_tools_modal" data-backdrop="true" data-keyboard="true"><?= _i('Report') ?></a>
    <?php if ($this->getAuth()->hasAccess('maccess.mod')) : ?>
        <a href="<?= $this->uri->create($radix->shortname . '/delete/' . $p->doc_id) ?>" class="btnr parent" data-function="delete" data-post="<?= $p->doc_id ?>" data-post-id="<?= $p->num ?>" data-board="<?= htmlspecialchars($p->radix->shortname) ?>" data-controls-modal="post_tools_modal" data-backdrop="true" data-keyboard="true"><?= _i('Delete') ?></a>
    <?php endif; ?>
</td>
</tr>
<?php
endforeach;
?>
</tbody>
</table>
</center>
</div>
</article>
