<?php

use phpOMS\Uri\UriFactory;
?>
<label for="<?= $this->id; ?>-sort-<?= $this->counter; ?>-up">
        <input
        id="<?= $this->id; ?>-sort-<?= $this->counter; ?>-up"
        type="radio"
        name="<?= $this->id; ?>-sort"
        <?= $this->id === $this->request->getData('element')
                && $this->request->getData('sort_order') === 'ASC'
                && $data[1] === ($this->request->getData('sort_by') ?? '')
                ? ' checked' : '';
                ?>>
        <i class="sort-asc lni lni-chevron-up"></i>
    </label>

    <label for="<?= $this->id; ?>-sort-<?= $this->counter; ?>-down">
        <input
        id="<?= $this->id; ?>-sort-<?= $this->counter; ?>-down"
        type="radio"
        name="<?= $this->id; ?>-sort"
        <?= $this->id === $this->request->getData('element')
                && $this->request->getData('sort_order') === 'DESC'
                && $data[1] === ($this->request->getData('sort_by') ?? '')
                ? ' checked' : '';
                ?>>
        <i class="sort-desc lni lni-chevron-down"></i>
</label>