<?php

function PokazPodstrone($pdo,$alias)
{
    $stmt = $pdo ->prepare('SELECT id,page_title, page_content FROM page_list WHERE alias = :alias AND status = 1 LIMIT 1');
    $stmt->execute([':alias'=> $alias]);
    $rekord = $stmt->fetch(PDO::FETCH_ASSOC);
    return $rekord !== false ? $rekord : null;
}
?>