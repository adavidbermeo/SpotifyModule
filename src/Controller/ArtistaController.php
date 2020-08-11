<?php 

/**
 * @file
 * Contains \Drupal\spotify_music\Controller\ArtistaController.
 */
 
namespace Drupal\spotify_music\Controller;
 
use Drupal\Core\Controller\ControllerBase;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI as Spotify;
use Drupal\Core\Routing;
 
class ArtistaController extends ControllerBase {

  public $artist_id_url;
  public $session;
  public $accessToken;
  public $api;
  public $artistTopTracks;
  public $artist_track_info;

  // Artist Data
  public $artist_name;
  public $artist_profile_picture;
  public $artist_main_info;

  public function callSpotifyAPI(){

    $this->artist_id_url = \Drupal::routeMatch()->getParameter('id');
    // kint($artist_id_url);

    // Set Session  

    $this->session = new Session(
      '65ea903d90354a90ac415ae3bf1679a2',
      'd4b986568b764385ac96ff1e0e00005f'
    );

    $this->session->requestCredentialsToken();
    $this->accessToken = $this->session->getAccessToken();

    if ($this->accessToken) {

      $this->session->setAccessToken($this->accessToken);
      
    }

    //Set Spotify API

    $this->api = new Spotify();


    $this->api->setAccessToken($this->accessToken);

  }

  public function getArtistMainInfo(){

    
    $this->artist_main_info = $this->api->getArtist($this->artist_id_url);


    $this->artist_name = $this->artist_main_info->name;
    $this->artist_profile_picture = $this->artist_main_info->images[0]->url;

    // print_r($artist_profile_picture);
  }

  public function getArtistTrackInfo(){  
    
    /**
    * Artist Top Tracks for /artist/{id}
    */


    $this->artistTopTracks = $this->api->getArtistTopTracks($this->artist_id_url, [
        'country' => 'se',
    ]);

    // kint($this->artistTopTracks);

    //Definir Variables 

    $album_names = [];
    $album_pictures = [];
    $tracks_names = [];
    $tracks_urls = [];


    foreach ($this->artistTopTracks->tracks as $track) {

        $album_names[] = $track->album->name;
        $album_pictures[] = $track->album->images[0]->url;
        $tracks_names[] = $track->name;
        $tracks_urls[] = $track->external_urls->spotify;
        
    }

      $this->artist_track_info = [
        'album_names' => $album_names,
        'album_pictures' => $album_pictures,
        'tracks_names' => $tracks_names,
        'tracks_urls' => $tracks_urls,
        'artist_name' => $this->artist_name,
        'artist_profile_picture' => $this->artist_profile_picture
    ];

    // kint($this->artist_track_info);

  }

    public function content() {

        $this->callSpotifyAPI();
        $this->getArtistMainInfo();
        $this->getArtistTrackInfo();

  
      return [
        '#theme' => 'artist_template',
        '#artist_info' => $this->artist_track_info,
        '#attached' =>[
          'library' => [
            'spotify_music/drupal.custom-libraries',
            'spotify_music/bootstrap-cdn',
          ],
        ],
      ];
      
      kint($this->artist_track_info);
    }
  }