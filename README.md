# e스포츠 포털 관리 시스템

## 프로젝트 개요
이 프로젝트는 Laravel 기반으로 제작한 개인 학습용 e스포츠 포털 관리 웹 애플리케이션입니다. 사용자 화면에서는 팀, 선수, 경기, 뉴스, 패치 노트를 조회할 수 있고, 관리자 권한 계정에서는 팀/선수/경기/뉴스 데이터를 등록·수정·삭제할 수 있습니다. 특히 `matches` 데이터를 기준으로 순위표를 계산해 보여주는 집계 로직을 포함하고 있어, 단순 게시판보다는 포털형 데이터 흐름을 구현하는 데 초점을 맞췄습니다. 라우팅, 미들웨어, Eloquent 관계, 파일 업로드, Blade 화면 구성을 함께 다루면서 Laravel MVC 구조를 학습하기 위한 목적으로 작성했습니다.

## 개발 목적
- Laravel MVC 구조 학습
- 팀, 경기, 뉴스 데이터 관리 흐름 구현
- 경기 결과 기반 순위 집계 로직 구현
- 관리자 인증 및 접근 제어 흐름 이해
- Blade 기반 화면 구성과 라우팅 처리 학습

## 주요 기능
실제 코드에서 확인되는 기능만 정리했습니다.

- 팀 관리
  - 팀 목록/상세 조회
  - 팀 등록, 수정, 삭제
  - 팀 로고 업로드(`storage/app/public/teams`) 및 삭제 시 파일 정리
- 경기 관리
  - 경기 목록/상세 조회
  - 경기 등록, 수정, 삭제
  - 상태(`scheduled`, `live`, `finished`, `canceled`) 및 점수/승리팀 관리
  - `finished` 상태에서 승리팀 미입력 시 점수 비교로 자동 계산
- 순위 집계
  - `status = finished` 경기만 반영
  - 홈/원정 데이터를 `union all`로 합친 뒤 팀별 집계
  - 경기 수, 승/패, 득점/실점, 득실차, 승률 계산
  - 리그/스테이지 필터, 정렬 기준 변경, 5분 캐시
- 뉴스 관리
  - 뉴스 목록/상세 조회
  - 뉴스 등록, 수정, 삭제
  - 카테고리 필터, 검색, 최신순/인기순 정렬
  - 표지 이미지 업로드(`storage/app/public/news`) 및 수정/삭제 시 파일 정리
- slug 라우팅
  - 뉴스 상세: `news/{news:slug}` 형태로 모델 바인딩
  - 패치노트 상세: `patch-notes/{patchNote:slug}`
  - 선수 상세: `players/{player:slug}`
- 관리자 인증
  - 관리자 전용 라우트(`/admin/*`)를 `auth`, `admin` 미들웨어로 보호
  - 관리자 대시보드 및 DB 브라우저 화면 제공
- 부가 기능
  - 선수 CRUD 및 선수 사진 업로드(`storage/app/public/players`)
  - 패치 노트 목록/상세 조회

## 기술 스택
프로젝트 파일(`composer.json`, `package.json`, Blade/라우트/컨트롤러 코드) 기준으로 정리했습니다.

- Backend
  - PHP 8.2
  - Laravel 12
  - Eloquent ORM
  - Laravel Middleware
- Database
  - 관계형 DB 기반 스키마(MySQL/MariaDB 사용을 전제로 한 쿼리 포함: `SHOW TABLES`)
- Frontend
  - Blade
  - HTML/CSS/JavaScript
  - Bootstrap 4 (정적 에셋 포함)
- Build / Tool
  - Composer
  - npm / Vite (스크립트 정의)
  - Git
- Library
  - intervention/image-laravel
  - phpoffice/phpspreadsheet
- 기타
  - Laravel Cache (`Cache::remember` 사용)
  - Laravel Storage (public 디스크 업로드/삭제)

## 프로젝트 구조
실제 폴더 기준 핵심 구조입니다.

```text
app/
  Http/
    Controllers/
      Admin/
    Middleware/
    Requests/
  Models/
bootstrap/
config/
database/
  migrations/
  seeders/
public/
resources/
  views/
routes/
  web.php
storage/
```

## DB 설계 요약
`database/migrations` 기준으로 작성했습니다.

| 테이블 | 주요 역할 | 주요 컬럼 |
| --- | --- | --- |
| accounts | 로그인 계정 저장 | id, name, email, password, remember_token |
| users | 기본 Laravel 사용자 테이블(프로젝트 auth 기본 provider와는 별도) | id, name, email, password |
| teams | 팀 정보 저장 | id, name, slug, region, founded_at, logo_url, is_active, meta |
| players | 선수 정보 저장 | id, name, ign, slug, role, country, birthdate, team_id, photo_url, is_active |
| matches | 경기 일정/결과 저장 | id, slug, title, team1_id, team2_id, best_of, start_at, status, team1_score, team2_score, winner_team_id, stage, league |
| categories | 뉴스 카테고리 저장 | id, name, slug |
| news | 뉴스 게시글 저장 | id, category_id, title, slug, excerpt, content, cover_path, source_url, is_pinned, published_at, views |
| patch_notes | 패치 노트 저장 | id, game, version, title, slug, published_at, hero_image |
| cache / cache_locks | 캐시 저장 | key, value, expiration |
| jobs / job_batches / failed_jobs | 큐/배치/실패 작업 저장 | queue, payload, attempts 등 |
| password_reset_tokens / sessions | 인증 보조 데이터 저장 | email/token, session payload 등 |

관계(코드/마이그레이션 기준으로 확인 가능한 범위)
- `players.team_id -> teams.id`
- `matches.team1_id`, `matches.team2_id`, `matches.winner_team_id -> teams.id`
- `news.category_id -> categories.id` (`nullOnDelete`)

## 핵심 구현 포인트

### 1. 경기 결과 기반 순위 집계
- 순위 집계는 `RankingsController@index`에서 수행합니다.
- `matches` 중 `status = finished` 데이터만 집계에 포함합니다.
- 홈팀(`team1`)과 원정팀(`team2`) 기준 데이터를 각각 같은 스키마로 만든 뒤 `unionAll`로 합칩니다.
- 이후 `fromSub + groupBy(team_id)`로 팀별 경기 수, 승/패, 득점/실점, 득실차, 승률을 계산합니다.
- 최종적으로 `teams`와 join하여 팀 이름/로고를 결합하고, 정렬 기준(`wins`, `winrate`, `diff`, `sf`, `sa`, `name`)을 동적으로 적용합니다.
- 결과와 리그/스테이지 옵션 목록은 `Cache::remember(..., 300, ...)`으로 5분 캐시합니다.

관련 위치
- `app/Http/Controllers/RankingsController.php`
- `routes/web.php` (`/rankings`)
- `resources/views/rankings/index.blade.php`

### 2. 관리자 인증 및 접근 제어
- 관리자 영역은 `routes/web.php`에서 `/admin` prefix + `auth`, `admin` 미들웨어 그룹으로 분리되어 있습니다.
- `AdminMiddleware`는 로그인 여부를 먼저 확인하고, `Auth::user()->is_admin` 값으로 관리자 권한을 검사합니다.
- 일반 사용자 접근 시 JSON 요청은 403, 일반 웹 요청은 이전 페이지 리다이렉트 + 경고 메시지 처리합니다.
- 각 도메인 컨트롤러(`TeamController`, `MatchController`, `NewsController`, `PlayerController`, `RankingsController`)도 `HasMiddleware`로 쓰기 기능에 동일한 보호를 적용하고, 목록/상세는 예외로 공개합니다.

관련 위치
- `routes/web.php`
- `app/Http/Middleware/AdminMiddleware.php`
- `bootstrap/app.php` (미들웨어 alias 등록)
- `app/Http/Controllers/Admin/AdminDashboardController.php`
- `app/Http/Controllers/Admin/AdminDbController.php`

### 3. 뉴스/팀/경기 데이터 관리와 slug 라우팅
- 관리자 권한 계정으로 뉴스/팀/경기(그리고 선수) 데이터를 등록·수정·삭제합니다.
- 뉴스는 `News` 모델의 `getRouteKeyName()`이 `slug`를 반환하여 상세 페이지가 slug 기반으로 연결됩니다.
- 패치노트/선수도 같은 방식으로 slug 라우팅을 사용합니다.
- 팀과 경기는 기본 리소스 라우트 바인딩으로 상세를 조회하고, 팀 상세에서 소속 선수 관계를 함께 로드합니다.
- 뉴스/팀/선수는 이미지 업로드 시 public 디스크를 사용하며, 뉴스/팀은 수정·삭제 시 기존 파일 정리 로직이 구현되어 있습니다.

관련 위치
- `app/Http/Controllers/NewsController.php`
- `app/Http/Controllers/TeamController.php`
- `app/Http/Controllers/MatchController.php`
- `app/Models/News.php`, `app/Models/PatchNote.php`, `app/Models/Player.php`

## 실행 방법
현재 저장소 기준 실행 순서입니다.

1. 저장소 클론
2. 의존성 설치
   - `composer install`
   - `npm install`
3. 환경 파일 준비
   - `.env` 생성(`.env.example` 복사)
   - DB 접속 정보 설정
4. 앱 키 생성
   - `php artisan key:generate`
5. 마이그레이션 실행
   - `php artisan migrate`
6. 스토리지 심볼릭 링크 생성(업로드 이미지 표시 필요 시)
   - `php artisan storage:link`
7. 프론트 에셋 개발 서버 실행(선택)
   - `npm run dev`
8. Laravel 서버 실행
   - `php artisan serve`

시드 데이터
- 시더 파일은 존재하지만 `DatabaseSeeder`는 현재 `PatchNoteSeeder`와 기본 `User` 팩토리를 호출합니다.
- 필요 시 개별 시더를 직접 실행하는 방식이 안전합니다.
  - 예: `php artisan db:seed --class=PatchNoteSeeder`

## 트러블슈팅 또는 학습 포인트
- 순위 데이터는 `finished` 경기만 집계해야 신뢰도 있는 결과를 유지할 수 있습니다.
- 홈/원정 데이터 구조가 다르기 때문에 순위표 집계 전 `union all` 정규화 단계가 필요합니다.
- 관리자 라우트는 반드시 미들웨어로 보호해야 하며, 컨트롤러 쓰기 액션도 별도로 보호하는 것이 안전합니다.
- slug 라우팅은 중복/누락 시 상세 페이지 연결 문제가 발생할 수 있어 저장 시 유니크 보정 로직이 필요합니다.
- 업로드 파일을 웹에서 노출하려면 `storage:link`와 저장 경로(`public` 디스크) 관리가 중요합니다.
- 코드상 `accounts` 인증 모델에서 `is_admin` 값을 사용하므로, 실제 DB 스키마에서도 해당 컬럼 정합성을 확인해야 합니다.

## 향후 개선점
- 경기 목록/순위 화면의 검색·필터 조건 확장
- 팀/선수 상세 통계 지표 추가
- 순위 집계 캐시 키/만료 정책 정리 및 무효화 전략 보강
- 관리자 권한 등급(예: 읽기 전용, 편집 가능) 분리
- FormRequest 기반 입력값 검증 범위 확대
- 테스트 코드(Pest/Laravel) 보강
- 로컬/배포 환경 설정 절차 문서화
