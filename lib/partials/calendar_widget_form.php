<div class="event-creator">

  <div class="form options-form">

    <?php foreach ($data['fields'] as $field) : ?>

      <div class="row">
        <div class="control-label">
          <labelf for="<?= $field['id'] ?>"><?= $field['label'] ?></label>
        </div>
        <div class="form-control">
          <input
            type="text"
            id="<?= $field['id'] ?>"
            class="control-field"
            name="<?= $field['calc_name'] ?>"
            value="<?= $field['value'] ?>"
          />
        </div>
      </div>

    <?php endforeach; ?>

  </div>
</div>
