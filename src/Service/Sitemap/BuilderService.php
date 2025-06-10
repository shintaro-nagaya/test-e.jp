<?php

namespace App\Service\Sitemap;

use App\Service\Entity\News;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BuilderService
{
    private array $url = [];

    public function __construct(
        private readonly ParameterBagInterface $parameterBag,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly News\EntryService $newsService
    ) {}

    /**
     * URL登録実行
     * @return void
     */
    public function registrationUrls(): void
    {
        foreach($this->parameterBag->get('sitemap_fixed') as $routeName) {
            if($routeName === "top") {
                $this->addUrlByRouteName($routeName, [], 1);
            } else {
                $this->addUrlByRouteName($routeName, []);
            }
        }

        $this->newsService->addSitemap($this);
    }

    /**
     * Route名からURLを登録、既存の場合は上書き
     * @param string $routeName
     * @param array $parameter
     * @param float $priority
     * @return $this
     */
    public function addUrlByRouteName(string $routeName, array $parameter = [], float $priority = 0.9): self
    {
        $uri = $this->urlGenerator->generate($routeName, $parameter, UrlGeneratorInterface::ABSOLUTE_URL);
        $this->url[$uri] = $priority;
        return $this;
    }

    public function createXml(string $changeFreq = "weekly"): string
    {
        $now = new \DateTime();
        $xml = <<< EOF
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

EOF;

        foreach($this->getUrl() as $uri => $priority) {
            $url = [
                "  <url>",
                "    <loc>". $uri. "</loc>",
                "    <lastmod>". $now->format("Y-m-d"). "</lastmod>",
                "    <changefreq>". $changeFreq. "</changefreq>",
                "    <priority>". $priority. "</priority>",
                "  </url>"
            ];
            $xml .= implode("\r\n", $url). "\r\n";
        }

        $xml .= "</urlset>";

        return $xml;
    }

    public function getUrl(): array
    {
        return $this->url;
    }
    public function response(string $xml): Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/xml');
        $response->setContent($xml);

        return $response;
    }

    public function exportSitemap(string $xml, string $filename = "sitemap.xml"): Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', "application/octet-stream");
        $response->headers->set('Content-Disposition', "attachment; filename=". $filename);
        $response->headers->set('Content-Length', strlen($xml));
        $response->setContent($xml);

        return $response;
    }

    public function dumpSitemap(string $xml, string $filename = "sitemap.xml"): string
    {
        $path = $this->parameterBag->get('document_root')."/". $filename;
        $fs = new Filesystem();
        $fs->dumpFile($path, $xml);

        return $path;
    }
}