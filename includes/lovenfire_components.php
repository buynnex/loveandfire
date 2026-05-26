<?php
if (!defined('HC_APP')) { die('Acesso negado'); }

require_once dirname(__FILE__) . '/onboarding_helpers.php';

function renderLovenHeader($title, $subtitle, $actionsHtml)
{
    echo '<header class="lfv2-topbar">';
    echo '<div class="lfv2-topbar-brand">';
    echo '<span class="lfv2-script">' . h(APP_NAME) . '</span>';
    echo '<strong>' . h($title) . '</strong>';
    if ($subtitle !== '') {
        echo '<small>' . h($subtitle) . '</small>';
    }
    echo '</div>';
    echo '<div class="lfv2-topbar-actions">' . $actionsHtml . '</div>';
    echo '</header>';
}

function renderLovenSidebar($active)
{
    $items = array(
        'feed' => array('feed.php', 'Feed'),
        'dashboard' => array('dashboard.php', 'Discover'),
        'chat' => array('chat.php', 'Mensagens'),
        'pets' => array('pets.php', 'LovenPets'),
        'profile' => array('profile_edit.php', 'Perfil')
    );

    echo '<aside class="lfv2-sidebar"><nav>';
    foreach ($items as $key => $item) {
        $isActive = $active === $key ? ' active' : '';
        echo '<a class="lfv2-side-link' . $isActive . '" href="' . h($item[0]) . '">' . h($item[1]) . '</a>';
    }
    echo '</nav></aside>';
}

function renderAuthLayout($title, $subtitle, $bodyHtml)
{
    echo '<section class="lfv2-auth-wrap">';
    echo '<div class="lfv2-auth-stage">';
    echo '<aside class="lfv2-auth-hero">';
    echo '<span class="lfv2-script">Love &amp; Fire</span>';
    echo '<h2>A chama continua de onde voce parou.</h2>';
    echo '<p>Conexoes premium, onboarding elegante e descobertas com mais intencao.</p>';
    echo '<div class="lfv2-auth-hero-chips">';
    echo '<span>Sala publica romantica</span>';
    echo '<span>Discover premium</span>';
    echo '<span>Fluxo seguro e privado</span>';
    echo '</div>';
    echo '</aside>';

    echo '<div class="lfv2-auth-card">';
    echo '<div class="lfv2-auth-brand">';
    echo '<span class="lfv2-script">' . h(APP_NAME) . '</span>';
    echo '</div>';
    echo '<h1>' . h($title) . '</h1>';
    if ($subtitle !== '') {
        echo '<p class="lfv2-auth-subtitle">' . h($subtitle) . '</p>';
    }
    echo '<div class="lfv2-auth-body">';
    echo $bodyHtml;
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</section>';
}

function renderProgressSteps($currentStep)
{
    $steps = lf_onboarding_steps();
    $currentStep = lf_onboarding_normalize_step($currentStep);
    $keys = array_keys($steps);
    $currentIndex = array_search($currentStep, $keys, true);

    echo '<ol class="lfv2-progress-steps">';
    foreach ($steps as $key => $label) {
        $idx = array_search($key, $keys, true);
        $class = 'todo';
        if ($idx < $currentIndex) { $class = 'done'; }
        if ($idx === $currentIndex) { $class = 'current'; }
        echo '<li class="' . h($class) . '"><span>' . h($label) . '</span></li>';
    }
    echo '</ol>';
}

function renderInterestChips($interests, $selectedIds)
{
    $selected = array();
    foreach ((array)$selectedIds as $id) {
        $selected[(int)$id] = true;
    }

    echo '<div class="lfv2-chip-grid">';
    foreach ((array)$interests as $row) {
        $id = (int)$row['id'];
        $isSelected = isset($selected[$id]) ? ' checked' : '';
        echo '<label class="lfv2-chip' . $isSelected . '">';
        echo '<input type="checkbox" name="interesse_ids[]" value="' . $id . '"' . ($isSelected ? ' checked' : '') . '>';
        echo '<span>' . h($row['nome']) . '</span>';
        echo '</label>';
    }
    echo '</div>';
}

function renderSelectableCard($fieldName, $value, $label, $description, $selectedValue)
{
    $checked = (string)$value === (string)$selectedValue;
    echo '<label class="lfv2-select-card' . ($checked ? ' active' : '') . '">';
    echo '<input type="radio" name="' . h($fieldName) . '" value="' . h($value) . '"' . ($checked ? ' checked' : '') . '>';
    echo '<strong>' . h($label) . '</strong>';
    if ($description !== '') {
        echo '<small>' . h($description) . '</small>';
    }
    echo '</label>';
}

function renderProfilePhotoUploader($currentPhoto)
{
    echo '<div class="lfv2-photo-upload">';
    if ($currentPhoto !== '') {
        echo '<img src="' . h($currentPhoto) . '" alt="Foto de perfil">';
    } else {
        echo '<div class="lfv2-photo-placeholder">Avatar temporario</div>';
    }
    echo '<input type="file" name="avatar_file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">';
    echo '<p>JPG, PNG ou WEBP ate 4MB.</p>';
    echo '</div>';
}

function renderSocialCardPreview($data)
{
    $name = isset($data['name']) ? $data['name'] : '';
    $nick = isset($data['apelido_publico']) ? $data['apelido_publico'] : '';
    $city = isset($data['city']) ? $data['city'] : '';
    $bio = isset($data['bio']) ? $data['bio'] : '';
    echo '<article class="lfv2-social-preview">';
    echo '<h3>' . h($name) . '</h3>';
    echo '<span>@' . h($nick) . '</span>';
    echo '<p>' . h($city) . '</p>';
    echo '<blockquote>' . h($bio) . '</blockquote>';
    echo '</article>';
}

function renderPrivacySwitchList($values)
{
    $items = array(
        'mostrar_cidade' => 'Mostrar cidade',
        'mostrar_distancia' => 'Mostrar distancia',
        'aparecer_discover' => 'Aparecer no Discover',
        'aparecer_salas_publicas' => 'Aparecer em salas publicas',
        'permitir_dm' => 'Permitir DM',
        'permitir_pedidos_dm' => 'Permitir pedidos de DM',
        'permitir_mensagens_compativeis' => 'Permitir mensagens compativeis',
        'modo_invisivel' => 'Modo invisivel',
        'receber_notificacoes' => 'Receber notificacoes',
        'receber_recomendacoes' => 'Receber recomendacoes'
    );

    echo '<div class="lfv2-switch-list">';
    foreach ($items as $key => $label) {
        $checked = !empty($values[$key]);
        echo '<label class="lfv2-switch-item">';
        echo '<span>' . h($label) . '</span>';
        echo '<input type="checkbox" name="' . h($key) . '" value="1"' . ($checked ? ' checked' : '') . '>';
        echo '</label>';
    }
    echo '</div>';
}

function renderFinalProfilePreview($context)
{
    $user = isset($context['user']) ? $context['user'] : array();
    $progress = isset($context['progress']) ? $context['progress'] : array();
    $missing = isset($context['missing']) ? $context['missing'] : array();

    echo '<section class="lfv2-final-preview">';
    echo '<h2>Preview final do perfil</h2>';
    echo '<p>Revise seus dados antes de concluir.</p>';
    echo '<dl>';
    echo '<dt>Nome</dt><dd>' . h(isset($user['name']) ? $user['name'] : '-') . '</dd>';
    echo '<dt>Apelido</dt><dd>' . h(isset($user['apelido_publico']) ? $user['apelido_publico'] : '-') . '</dd>';
    echo '<dt>Cidade</dt><dd>' . h(isset($user['city']) ? $user['city'] : '-') . '</dd>';
    echo '<dt>Contato</dt><dd>' . h(!empty($user['email']) ? $user['email'] : (isset($user['telefone']) ? $user['telefone'] : '-')) . '</dd>';
    echo '</dl>';

    if (!empty($progress)) {
        echo '<h3>Etapas salvas</h3><ul class="lfv2-inline-list">';
        foreach ($progress as $step => $row) {
            if (!empty($row['concluida'])) {
                echo '<li>' . h($step) . '</li>';
            }
        }
        echo '</ul>';
    }

    if (!empty($missing)) {
        echo '<div class="lfv2-error-box"><strong>Faltam campos obrigatorios:</strong> ' . h(implode(', ', $missing)) . '</div>';
    } else {
        echo '<div class="lfv2-ok-box">Tudo pronto para ativar seu perfil.</div>';
    }
    echo '</section>';
}

function renderFormError($message)
{
    if ($message === '') {
        return;
    }
    echo '<div class="lfv2-error-box">' . h($message) . '</div>';
}

function renderSaveProgressNotice($message)
{
    if ($message === '') {
        return;
    }
    echo '<div class="lfv2-save-box">' . h($message) . '</div>';
}
