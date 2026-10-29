<?php
    // Escapa valores antes de inseri-los em HTML (conteúdo ou atributo).
    // ENT_SUBSTITUTE evita que um texto com UTF-8 inválido vire string vazia.
    function e($valor): string
    {
        return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
