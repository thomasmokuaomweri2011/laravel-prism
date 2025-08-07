<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Prism;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Http;

class BrochureController extends Controller
{
    public function index()
    {
        return view('brochure.index');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'urls' => 'required|array',
            'urls.*' => 'url',
        ]);

        $brochures = [];

        foreach ($request->urls as $url) {
            $html = Http::get($url)->body();
            $crawler = new Crawler($html);

            $title = $crawler->filter('title')->count() > 0
                ? $crawler->filter('title')->first()->text()
                : null;

            $metaDesc = $crawler->filter('meta[name="description"]')->count() > 0
                ? $crawler->filter('meta[name="description"]')->first()->attr('content')
                : null;

            $textContent = $crawler->filter('body')->text();
            $shortContent = substr($textContent, 0, 4000);

            $prompt = <<<PROMPT
Return only valid JSON with this schema:
{
  "english": {
    "title": "string",
    "tagline": "string",
    "mission": "string",
    "services": ["service 1", "service 2"],
    "contact": "string"
  },
  "swahili": {
    "title": "string",
    "tagline": "string",
    "mission": "string",
    "services": ["service 1", "service 2"],
    "contact": "string"
  },
  "logo": "absolute url string"
}

Website URL: $url
Website Title: $title
Meta Description: $metaDesc

Page Content:
$shortContent
PROMPT;

            $response = Prism::text()
                ->using(Provider::OpenAI, 'gpt-4o-mini')
                ->withSystemPrompt('Return only valid JSON matching the schema. Do not include explanations.')
                ->withPrompt($prompt)
                ->asText();

            $clean = preg_replace('/^```json\s*|\s*```$/', '', trim($response->text));
            $parsed = json_decode($clean, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $parsed = [];
            }

            foreach (['english', 'swahili'] as $lang) {
                if (isset($parsed[$lang]['services']) && is_string($parsed[$lang]['services'])) {
                    $parsed[$lang]['services'] = preg_split('/[\n,;]+/', $parsed[$lang]['services']);
                }
                if (!isset($parsed[$lang]['services'])) {
                    $parsed[$lang]['services'] = [];
                }
            }

            $logo = null;
            if (!empty($parsed['logo']) && filter_var($parsed['logo'], FILTER_VALIDATE_URL)) {
                $logo = $parsed['logo'];
            }
            if (!$logo && $crawler->filter('img')->count() > 0) {
                foreach ($crawler->filter('img') as $img) {
                    $src = $img->getAttribute('src');
                    $alt = strtolower($img->getAttribute('alt') ?? '');
                    $class = strtolower($img->getAttribute('class') ?? '');
                    if (str_contains($alt, 'logo') || str_contains($class, 'logo')) {
                        $logo = $src;
                        break;
                    }
                }
            }
            if ($logo && str_starts_with($logo, '/')) {
                $parsedUrl = parse_url($url);
                $logo = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $logo;
            }

            $brochures[] = [
                'url' => $url,
                'logo' => $logo,
                'english' => $parsed['english'] ?? [],
                'swahili' => $parsed['swahili'] ?? [],
            ];
        }

        session(['brochures' => $brochures]);

        return view('brochure.generated', compact('brochures'));
    }

    public function download(int $index)
    {
        $brochures = session('brochures', []);
        if (!isset($brochures[$index])) {
            abort(404);
        }

        $brochure = $brochures[$index];
        $pdf = Pdf::loadView('brochure.pdf', ['brochure' => $brochure]);

        return $pdf->download('brochure.pdf');
    }
}
