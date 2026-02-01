<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Authentication\Identifier;

use Authentication\Identifier\TokenIdentifier;
use Cake\Utility\Security;

/**
 * Token identifier with fallback for hashed and unhashed tokens
 */
class TokenFallbackIdentifier extends TokenIdentifier
{

    public function identify(array $credentials, array $options = [])
    {

        $dataField = $this->getConfig('dataField');
        if (!isset($credentials[$dataField])) {
            return null;
        }

        $conditions = [];
        $conditions[] = $credentials[$dataField];

        if ($this->getConfig('hashAlgorithm') !== null) {
            $hashedToken= Security::hash(
                $credentials[$dataField],
                $this->getConfig('hashAlgorithm')
            );
            $conditions[] = $hashedToken;
        }

        $conditions = [$this->getConfig('tokenField') => $conditions];
        return $this->getResolver()->find($conditions);

    }

}
