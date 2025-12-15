<?php

function getSocialMediaAll($pdo, $keyword = null)
{
    $sql = "SELECT id_social_media, link, created_by FROM social_media";
    if ($keyword) {
        $sql .= " WHERE link ILIKE :keyword";
    }
    $sql .= " ORDER BY id_social_media ASC";

    $stmt = $pdo->prepare($sql);
    if ($keyword) {
        $stmt->bindValue(':keyword', "%$keyword%");
    }
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getSocialMediaById($pdo, $id)
{
    $stmt = $pdo->prepare("SELECT id_social_media, link FROM social_media WHERE id_social_media = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function insertSocialMedia($pdo, $link, $adminId)
{
    $stmt = $pdo->prepare("
        INSERT INTO social_media (link, created_by)
        VALUES (?, ?)
    ");
    return $stmt->execute([$link, $adminId]);
}

function updateSocialMedia($pdo, $id, $link)
{
    $stmt = $pdo->prepare("
        UPDATE social_media SET link = ?
        WHERE id_social_media = ?
    ");
    return $stmt->execute([$link, $id]);
}

function deleteSocialMedia($pdo, $id)
{
    $stmt = $pdo->prepare("DELETE FROM social_media WHERE id_social_media = ?");
    return $stmt->execute([$id]);
}