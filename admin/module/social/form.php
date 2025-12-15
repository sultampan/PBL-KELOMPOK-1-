<?php
// admin/module/social/form.php
?>

<div class="card">
    <div class="card-header-flex">
        <h2>Atur Link Social Media</h2>
    </div>

    <form id="socialForm" method="POST" style="margin-top: 20px;">
        
        <div class="social-list-container">
            <?php foreach ($socials as $soc): ?>
                
                <div class="social-row">
                    
                    <div class="col-identity">
                        <span class="social-label">
                            <?= $soc['nama_platform'] ?>
                        </span>
                    </div>

                    <div class="col-input">
                        <input type="text" name="links[<?= $soc['id_social'] ?>]" 
                               class="social-input" 
                               value="<?= htmlspecialchars($soc['link_url']) ?>" 
                               placeholder="https://...">
                    </div>

                    <div class="col-toggle">
                        <label class="switch" title="Aktifkan/Nonaktifkan">
                            <input type="checkbox" name="active[<?= $soc['id_social'] ?>]" 
                                   <?= $soc['is_active'] ? 'checked' : '' ?>>
                            <span class="slider"></span>
                        </label>
                    </div>

                </div>

            <?php endforeach; ?>
        </div>

        <div class="button-group" style="margin-top: 25px; display: flex; justify-content: flex-end;">
            <button type="submit" id="submitBtn" class="btn btn-primary" 
                    style="padding: 12px 30px; font-weight: bold; border-radius: 6px;">
                Simpan Perubahan
            </button>
        </div>

    </form>
</div>