# 4Byte.dev

> Uygulayıcılar için makine öğrenimi ve yapay zeka kavramlarının net, kısa ve öz açıklamaları.

## Hakkında

4Byte.dev, makine öğrenimi ve yapay zeka kavramlarına odaklanan teknik bir dokümantasyon web sitesidir. ML mimarileri, algoritmaları ve uygulama kalıpları hakkında iyi yapılandırılmış, anlaşılması kolay açıklamalar sunmayı hedefler.

## Kullanılan Teknolojiler (Tech Stack)

- **Çerçeve (Framework)**: [Astro](https://astro.build) v6
- **Stillendirme**: [UnoCSS](https://unocss.dev) (Tailwind uyumlu)
- **İkonlar**: [Lucide](https://lucide.dev)
- **Diller**: TypeScript + Astro bileşenleri

## Hızlı Başlangıç

```bash
# Bağımlılıkları yükle
pnpm install

# Geliştirme sunucusunu başlat
pnpm dev

# Üretim (production) için derle
pnpm build

# Üretim derlemesini önizle
pnpm preview
```

## Komutlar (Scripts)

| Komut                | Açıklama                                       |
| -------------------- | ---------------------------------------------- |
| `pnpm dev`           | Yerel geliştirme sunucusunu başlatır           |
| `pnpm build`         | Üretim için derler                             |
| `pnpm preview`       | Üretim derlemesini önizler                     |
| `pnpm generate:og`   | OG (Open Graph) görselleri oluşturur           |
| `pnpm generate:data` | Veri dosyalarını günceller                     |
| `pnpm lint`          | ESLint'i çalıştırır                            |
| `pnpm lint:fix`      | ESLint hatalarını düzeltir                     |
| `pnpm format`        | Prettier ile kodu formatlar                    |
| `pnpm format:check`  | Kod formatını (biçimlendirmesini) kontrol eder |

## Özellikler

- **Çoklu Dil Desteği**: Türkçe (varsayılan) ve İngilizce
- **Karanlık/Aydınlık Mod**: Sistem tercihi algılama
- **Arama**: İstemci tarafı (client-side) arama işlevselliği
- **SEO Optimizasyonlu**: Site haritası (Sitemap), RSS, Open Graph, Schema.org
- **Duyarlı (Responsive) Tasarım**: Mobil uyumlu sayfa düzeni
- **Statik Üretim (Static Generation)**: Hızlı performans

## Proje Yapısı

```
src/
├── components/     # Astro bileşenleri
├── content/        # Markdown makaleleri (en/, tr/)
├── data/           # JSON veri dosyaları
├── i18n/           # Uluslararasılaştırma (i18n) araçları
├── layouts/        # Sayfa düzenleri (layouts)
├── pages/          # Rota sayfaları (sayfa yönlendirmeleri)
└── styles/         # Global CSS
```
