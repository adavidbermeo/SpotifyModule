<?php
/**
 * @file
 * Contains \Drupal\spotify_music\Controller\LanzamientosController.
 */
 
namespace Drupal\spotify_music\Controller;
 
use Drupal\Core\Controller\ControllerBase;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI as Spotify;

 
class LanzamientosController extends ControllerBase {

  public $session;
  public $accessToken;
  public $api;
  public $releases;
  public $new_releases_array;


  public function callSpotifyAPI(){

    $this->session = new Session(
      '65ea903d90354a90ac415ae3bf1679a2',
      'd4b986568b764385ac96ff1e0e00005f'
    );

    $this->session->requestCredentialsToken();
    $this->accessToken = $this->session->getAccessToken();

    if ($this->accessToken) {

      $this->session->setAccessToken($this->accessToken);
      
    }

  }

  public function getNewReleases(){

    $this->api = new Spotify();


    $this->api->setAccessToken($this->accessToken);
  
  
    /**
     * 
     * Getting new Releases
     * 
     */
  
  
    $this->releases = $this->api->getNewReleases([
        'country' => 'se',
    ]);
  
  
    $artist_names = [];
    $artist_ids = [];
    $track_names = [];
    $track_images = [];
    $track_spotify_urls = [];
  
    foreach ($this->releases->albums->items as $album) {
  
        $artist_names[] = $album->artists[0]->name;
        $artist_ids[] = $album->artists[0]->id;
  
        $track_names[] = $album->name; 
        $track_images[] =  $album->images[0]->url; 
        
  
        $track_spotify_urls[] = $album->external_urls->spotify;
  
    }
  
    $this->new_releases_array = [
        'track_image' => $track_images,
        'track_name' => $track_names,
        'artist_names' => $artist_names,
        'artist_ids' => $artist_ids,
        'track_spotify_urls' => $track_spotify_urls
    ];
  
  
    // print_r($new_releases_array);
  }

  public function content() {

    // $custom_array = ['Angel', 'Maria'];

    $this->callSpotifyAPI();
    $this->getNewReleases();


    return $renderable = [
      '#theme' => 'my_template',
      '#test_var' => ['spotify_releases' => $this->new_releases_array],
      '#attached' =>[
        'library' => [
          'spotify_music/drupal.custom-libraries',
          'spotify_music/bootstrap-cdn',
        ],
      ],
    ];
    // $rendered = \Drupal::service('renderer')->renderPlain($renderable);

    }
}