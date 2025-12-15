<?php

function getSocialMediaAll(PDO $pdo, $keyword = null)
{
    if ($keyword) {
        $stmt = $pdo->prepare("
            SELECT * FROM social_media
            WHERE nama ILIKE :keyword
            ORDER BY urutan ASC, id_social_media DESC
        ");
        $stmt->execute(['keyword' => "%$keyword%"]);
    } else {
        $stmt = $pdo->query("
            SELECT * FROM social_media
            ORDER BY urutan ASC, id_social_media DESC
        ");
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getSocialMediaById(PDO $pdo, $id)
{
    $stmt = $pdo->prepare("SELECT * FROM social_media WHERE id_social_media = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function insertSocialMedia($pdo, $data)
{
    $sql = "INSERT INTO social_media
        (nama, icon, link, tipe_icon, is_active, urutan, created_by)
        VALUES
        (:nama, :icon, :link, :tipe_icon, :is_active, :urutan, :created_by)";

    $stmt = $pdo->prepare($sql);
    return $stmt->execute($data);
}

function updateSocialMedia($pdo, $id, $data)
{
    $sql = "UPDATE social_media SET
        nama = :nama,
        icon = :icon,
        link = :link,
        tipe_icon = :tipe_icon,
        is_active = :is_active,
        urutan = :urutan
        WHERE id_social_media = :id";

    $data['id'] = $id;
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($data);
}



function deleteSocialMedia(PDO $pdo, $id)
{
    $stmt = $pdo->prepare("DELETE FROM social_media WHERE id_social_media = :id");
    $stmt->execute(['id' => $id]);
}
