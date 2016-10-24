<select id="languageid" name="languageid" class="form-control">
    <option value="none" disabled>Please select...</option>
    <?php foreach($this->view->languages as $language): ?>
        <option value="<?= $language->languageId; ?>"><?= $language->nativeName; ?></option>
    <?php endforeach; ?>
</select>