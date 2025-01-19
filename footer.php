<?php
// footer.php
?>
<style>
.site-footer {
    background-color: var(--card-background, #f8f9fa);
    color: var(--text-color, #333);
    padding: 2rem 1rem;
    margin-top: 2rem;
    font-size: 14px;
}

.site-footer-content {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    max-width: 1200px;
    margin: 0 auto;
    gap: 2rem;
}

.site-footer-section {
    flex: 1;
    min-width: 200px;
}

.site-footer-section h3 {
    font-size: 1.1rem;
    margin-bottom: 1rem;
    color: var(--primary-color, #007bff);
}

.site-footer-section ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

.site-footer-section ul li {
    margin-bottom: 0.5rem;
}

.site-footer-section ul li a {
    color: var(--text-color, #333);
    text-decoration: none;
    transition: color 0.3s ease;
}

.site-footer-section ul li a:hover {
    color: var(--primary-color, #007bff);
}

.site-footer-social-icons {
    display: flex;
    gap: 1rem;
}

.site-footer-social-icon {
    color: var(--text-color, #333);
    transition: color 0.3s ease;
}

.site-footer-social-icon:hover {
    color: var(--primary-color, #007bff);
}

.site-footer-bottom {
    text-align: center;
    margin-top: 2rem;
    padding-top: 1rem;
    border-top: 1px solid var(--secondary-color, #dee2e6);
}

@media (max-width: 768px) {
    .site-footer-content {
        flex-direction: column;
        gap: 1.5rem;
    }

    .site-footer-section {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .site-footer {
        padding: 1rem;
    }

    .site-footer-content {
        gap: 1rem;
    }

    .site-footer-section h3 {
        font-size: 1rem;
    }

    .site-footer-social-icons svg {
        width: 20px;
        height: 20px;
    }

    .site-footer-bottom {
        font-size: 12px;
    }
}
</style>

