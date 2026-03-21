# 4Byte.dev'e Katkıda Bulunma

4Byte.dev'e katkıda bulunmaya gösterdiğiniz ilgi için teşekkür ederiz!

## Nasıl Katkıda Bulunulur

### Makale Ekleme

Makaleler Markdown formatında yazılır ve `src/content/articles/` dizinine yerleştirilir.

#### Dizin Yapısı

```
src/content/articles/
├── en/  # İngilizce makaleler
└── tr/  # Türkçe makaleler
```

#### Frontmatter (Ön Madde)

Her makale aşağıdaki frontmatter bilgilerini gerektirir:

```markdown
---
lang: 'tr' # Türkçe için 'tr', İngilizce için 'en'
slug: 'makale-slug-adresi' # Slug tanımlayıcı
title: 'Makale Başlığı'
excerpt: 'Makalenin kısa açıklaması'
category: 'kategori-adi'
tags: ['etiket1', 'etiket2']
author: '@yazar' # Yazarın github kullanıcı adı
date: '2026-03-20'
views: 0
status: 'Published' # 'Published' (Yayınlanmış) veya 'Draft' (Taslak)
---

Makale içeriği buraya gelecek...
```

#### Kategoriler

Mevcut kategorileri src/data/categories.json dosyasından kontrol edebilirsiniz.

#### Etiketler

Mevcut etiketleri src/data/tags.json dosyasından kontrol edebilir veya yenilerini ekleyebilirsiniz.

Eğer yeni öğeler ekliyorsanız, veri üretiminden sonra PR iş akışında bunları renk kodlarıyla belirtmeyi ve açıklamayı unutmayın.

#### Yazım Kuralları

1. **Öz olun** - Kavramları gereksiz laf kalabalığı yapmadan net bir şekilde açıklayın
2. **Kod örnekleri kullanın** - Uygun olan yerlerde pratik örneklere yer verin
3. **Çapraz referans yapın** - İlgili makalelere bağlantı verin
4. **Gözden geçirin** - Göndermeden önce yazım ve dilbilgisi kurallarını kontrol edin
5. **Her iki dili de güncelleyin** - Mümkün olduğunda, makaleleri hem Türkçe hem de İngilizce olarak sunun

### Geliştirme

1. Depoyu (repository) fork'layın
2. Fork'unuzu klonlayın: `git clone https://github.com/KULLANICI_ADINIZ/4byte-astro.git`
3. Bağımlılıkları yükleyin: `pnpm install`
4. Bir dal (branch) oluşturun: `git checkout -b feature/yeni-ozelliginiz`
5. Değişikliklerinizi yapın
6. Lint işlemini çalıştırın: `pnpm lint`
7. Kodu formatlayın: `pnpm format`
8. Değişikliklerinizi commit'leyin
9. Fork'unuza push'layın
10. Bir Pull Request açın

### Kod Stili

- Projedeki mevcut kod kalıplarını takip edin
- Tip güvenliği için TypeScript kullanın
- Commit atmadan önce `pnpm lint` ve `pnpm format` komutlarını çalıştırın

## Sorun Bildirme

- GitHub Issues sekmesini kullanın
- Yenilerini oluşturmadan önce mevcut sorunları arayın
- Hatalar için net yeniden üretme (reproduction) adımları sağlayın

## Sorularınız mı var?

Herhangi bir sorunuz varsa GitHub'da bir tartışma açmaktan çekinmeyin.
